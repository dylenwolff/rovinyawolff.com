from pathlib import Path

from PIL import Image, ImageDraw, ImageFont


ROOT = Path(__file__).resolve().parents[1]
BRAND = ROOT / "public" / "images" / "brand"


def render(size: int) -> Image.Image:
    scale = size / 64
    image = Image.new("RGBA", (size, size), "#07111f")
    draw = ImageDraw.Draw(image)

    radius = round(14 * scale)
    draw.rounded_rectangle((0, 0, size - 1, size - 1), radius=radius, fill="#07111f")

    def points(values):
        return [(round(x * scale), round(y * scale)) for x, y in values]

    draw.polygon(points([(30.56, 10.44), (30.13, 20.35), (21.09, 29.39), (10.44, 30.56), (30.56, 30.56)]), fill="#f8fafc")
    draw.polygon(points([(33.44, 10.44), (33.87, 20.35), (42.91, 29.39), (53.56, 30.56), (33.44, 30.56)]), fill="#22d3ee")
    draw.polygon(points([(10.44, 33.44), (21.09, 34.61), (30.13, 43.65), (30.56, 53.56), (30.56, 33.44)]), fill="#60a5fa")
    draw.polygon(points([(33.44, 33.44), (33.44, 53.56), (33.87, 43.65), (42.91, 34.61), (53.56, 33.44)]), fill="#2563eb")
    return image


BRAND.mkdir(parents=True, exist_ok=True)
render(512).save(BRAND / "icon-512.png", optimize=True)
render(192).save(BRAND / "icon-192.png", optimize=True)
render(180).save(BRAND / "apple-touch-icon.png", optimize=True)
render(64).save(
    ROOT / "public" / "favicon.ico",
    format="ICO",
    sizes=[(16, 16), (32, 32), (48, 48), (64, 64)],
)


def font(size: int, bold: bool = False) -> ImageFont.FreeTypeFont:
    family = "segoeuib.ttf" if bold else "segoeui.ttf"
    return ImageFont.truetype(Path("C:/Windows/Fonts") / family, size)


social = Image.new("RGB", (1200, 630), "#07111f")
social_draw = ImageDraw.Draw(social)
social_draw.ellipse((820, -210, 1380, 350), fill="#0c2a51")
social_draw.ellipse((900, 310, 1260, 670), fill="#0b3852")
social_draw.rounded_rectangle((72, 70, 184, 182), radius=26, fill="#0d1929")
social.alpha_composite(render(94), (81, 79)) if social.mode == "RGBA" else social.paste(render(94), (81, 79), render(94))
social_draw.text((72, 238), "DYLEN ANDREW WOLFF", font=font(28, True), fill="#60a5fa")
social_draw.text((72, 292), "Systems Engineer", font=font(66, True), fill="#f8fafc")
social_draw.text((72, 374), "Digital Solutions Developer", font=font(48, True), fill="#22d3ee")
social_draw.text((72, 472), "Practical technology built around real problems.", font=font(27), fill="#cbd5e1")
social_draw.text((72, 552), "dylenwolff.com", font=font(24, True), fill="#f8fafc")
social.save(BRAND / "social-card.png", optimize=True)
