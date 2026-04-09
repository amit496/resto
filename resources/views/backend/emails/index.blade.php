@extends('admin.layout.index')
@section('title', 'Email Center')
@section('content')
    <div class="mb-4 d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h3 class="mb-1">Email Center</h3>
            <p class="text-muted mb-0">Compose emails, add attachments, and send them to multiple recipients with CC and BCC.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.emails.send') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">To</label>
                        <textarea class="form-control" name="to" rows="3" placeholder="name@example.com, another@example.com">{{ old('to') }}</textarea>
                        <div class="form-text">Separate multiple emails with commas, semicolons, or new lines.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CC</label>
                        <textarea class="form-control" name="cc" rows="2" placeholder="cc@example.com">{{ old('cc') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">BCC</label>
                        <textarea class="form-control" name="bcc" rows="2" placeholder="bcc@example.com">{{ old('bcc') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Email subject">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Message</label>
                        <textarea name="body" class="form-control" rows="8" placeholder="Write your email message here">{{ old('body') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Attachments</label>
                        <input type="file" name="attachments[]" class="form-control" multiple>
                        <div class="form-text">You can attach multiple files. Max size per file: 10 MB.</div>
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-primary px-4">Send Email</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Sent History</h5>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>To</th>
                        <th>CC / BCC</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th class="text-end">By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $email->subject }}</div>
                                <div class="text-muted small">Attachments: {{ count($email->attachments ?? []) }}</div>
                            </td>
                            <td class="small">
                                {{ implode(', ', $email->to_recipients ?? []) }}
                            </td>
                            <td class="small">
                                <div><strong>CC:</strong> {{ implode(', ', $email->cc_recipients ?? []) ?: '-' }}</div>
                                <div><strong>BCC:</strong> {{ implode(', ', $email->bcc_recipients ?? []) ?: '-' }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $email->status === 'sent' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($email->status) }}
                                </span>
                            </td>
                            <td>{{ optional($email->sent_at)->format('d M Y, h:i A') ?: '-' }}</td>
                            <td class="text-end">{{ $email->sender?->name ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No email has been sent yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $emails->links() }}
        </div>
    </div>
@endsection
