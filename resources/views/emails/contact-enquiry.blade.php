New website enquiry

Name: {{ $contact->name }}
Email: {{ $contact->email }}
Subject: {{ $contact->subject ?: 'General enquiry' }}

Message:
{{ $contact->message }}
