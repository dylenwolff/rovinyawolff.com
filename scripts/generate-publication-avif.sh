#!/usr/bin/env bash
set -euo pipefail

pdf_root="${1:-storage/app/public/projects/pdfs}"
page_root="${2:-storage/app/public/projects/pages}"
force="${FORCE:-0}"

mkdir -p "$page_root"

find "$pdf_root" -maxdepth 1 -type f -iname '*.pdf' -print0 | while IFS= read -r -d '' pdf; do
    slug="$(basename "${pdf%.*}")"
    output="$page_root/$slug"
    manifest="$output/manifest.json"

    if [[ -f "$manifest" && "$force" != "1" ]]; then
        echo "Skipping $slug (already optimized)"
        continue
    fi

    echo "Optimizing $slug"
    scratch="$(mktemp -d)"
    trap 'rm -rf "$scratch"' EXIT
    mkdir -p "$output"

    gs -q -dSAFER -dBATCH -dNOPAUSE -dUseCropBox \
        -sDEVICE=png16m -r110 -dTextAlphaBits=4 -dGraphicsAlphaBits=4 \
        -sOutputFile="$scratch/page-%03d.png" "$pdf"

    rm -f "$output"/page-*.avif
    export output
    find "$scratch" -maxdepth 1 -type f -name 'page-*.png' -print0 | \
        xargs -0 -P "${AVIF_JOBS:-4}" -I '{}' bash -c '
            source="$1"
            name="$(basename "${source%.png}").avif"
            convert "$source" -strip -quality 48 -define heic:speed=8 "$output/$name"
        ' _ '{}'

    first="$(find "$output" -maxdepth 1 -type f -name 'page-*.avif' | sort | head -n 1)"
    dimensions="$(identify -format '%w %h' "$first")"
    read -r width height <<< "$dimensions"

    mapfile -t files < <(find "$output" -maxdepth 1 -type f -name 'page-*.avif' -printf '%f\n' | sort)
    {
        printf '{\n  "format": "avif",\n  "width": %s,\n  "height": %s,\n  "pages": [\n' "$width" "$height"
        for index in "${!files[@]}"; do
            comma=','
            [[ "$index" -eq "$((${#files[@]} - 1))" ]] && comma=''
            printf '    "%s"%s\n' "${files[$index]}" "$comma"
        done
        printf '  ]\n}\n'
    } > "$manifest.tmp"
    mv "$manifest.tmp" "$manifest"

    rm -rf "$scratch"
    trap - EXIT
    echo "Created ${#files[@]} AVIF pages for $slug"
done
