@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | My Addresses')

@section('content')
    <section class="page-hero">
        <div class="container page-hero-box">
            <div class="eyebrow">My Account</div>
            <h1 class="section-title">Saved Addresses</h1>
            <p class="section-copy">Add multiple delivery addresses with landmark and instructions for faster delivery.</p>
        </div>
    </section>

    <section class="section-sm">
        <div class="container commerce-shell">
            <div class="commerce-card">
                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="hero-actions" style="justify-content: space-between; align-items: center;">
                    <div>
                        <a class="btn-outline" href="{{ route('frontend.account.profile') }}">Back to Profile</a>
                    </div>
                    <button type="button" class="btn" data-open-create>Create New Address</button>
                </div>

                <div style="margin-top:16px;" class="grid-2">
                    @forelse($addresses as $address)
                        <div class="feature-card">
                            <div class="menu-meta" style="margin-bottom:10px;">
                                <span class="pill">{{ strtoupper($address->type) }}</span>
                                @if($address->is_default)
                                    <span class="pill" style="background: rgba(245,158,11,0.14); color:#92400e;">DEFAULT</span>
                                @endif
                            </div>
                            <h3 style="margin:0 0 6px;">{{ $address->label ?: ucfirst($address->type) }}</h3>
                            <p class="muted" style="margin:0 0 8px;">
                                {{ $address->recipient_name ?: $customer->name }}@if($address->phone || $customer->phone) · {{ $address->phone ?: $customer->phone }}@endif
                            </p>
                            <p class="muted" style="margin:0;">{{ $address->formatted() }}</p>
                            @if($address->instructions)
                                <small class="muted">Instructions: {{ $address->instructions }}</small>
                            @endif

                            <div class="hero-actions" style="margin-top:12px; flex-wrap: wrap;">
                                @if(! $address->is_default)
                                    <form method="POST" action="{{ route('frontend.account.addresses.default', $address) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn-outline" type="submit">Make Default</button>
                                    </form>
                                @endif
                                <button type="button" class="btn-outline"
                                    data-open-edit
                                    data-address='@json($address)'>
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('frontend.account.addresses.destroy', $address) }}" onsubmit="return confirm('Delete this address?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-outline" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="feature-card">
                            No saved addresses yet. Click "Create New Address" to add one.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="commerce-sticky">
                <div class="commerce-card">
                    <div class="section-title" style="margin-top:0;">Tips for fast delivery</div>
                    <div class="summary-stack">
                        <div class="summary-row"><span class="muted">Landmark</span><strong>Gate / building / nearby shop</strong></div>
                        <div class="summary-row"><span class="muted">Phone</span><strong>Active number for rider call</strong></div>
                        <div class="summary-row"><span class="muted">Instructions</span><strong>Floor, entry, directions</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <dialog class="modal" id="addressModal">
        <form method="POST" action="{{ route('frontend.account.addresses.store') }}" id="addressForm" class="commerce-card" style="max-width: 760px; margin: 0 auto;">
            @csrf
            <input type="hidden" name="_method" value="POST" id="methodField">
            <div class="section-title" style="margin-top:0;" id="modalTitle">Create Address</div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="type">Type</label>
                    <select id="type" name="type" class="select" required>
                        <option value="home">Home</option>
                        <option value="work">Work</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="label">Label</label>
                    <input id="label" name="label" class="input" type="text" placeholder="Home / Office / Warehouse">
                </div>
                <div class="form-field">
                    <label for="recipient_name">Recipient Name</label>
                    <input id="recipient_name" name="recipient_name" class="input" type="text" placeholder="Receiver name">
                </div>
                <div class="form-field">
                    <label for="phone">Contact Number</label>
                    <input id="phone" name="phone" class="input" type="text" placeholder="10-digit mobile">
                </div>

                <div class="form-field full">
                    <label for="address_line_1">Address Line 1</label>
                    <textarea id="address_line_1" name="address_line_1" class="textarea" required placeholder="House/Flat, Street, Area"></textarea>
                </div>
                <div class="form-field full">
                    <label for="address_line_2">Address Line 2</label>
                    <textarea id="address_line_2" name="address_line_2" class="textarea" placeholder="Society, Block, etc (optional)"></textarea>
                </div>

                <div class="form-field">
                    <label for="landmark">Landmark</label>
                    <input id="landmark" name="landmark" class="input" type="text" placeholder="Near ...">
                </div>
                <div class="form-field">
                    <label for="city">City</label>
                    <input id="city" name="city" class="input" type="text">
                </div>
                <div class="form-field">
                    <label for="state">State</label>
                    <input id="state" name="state" class="input" type="text">
                </div>
                <div class="form-field">
                    <label for="postal_code">Pincode</label>
                    <input id="postal_code" name="postal_code" class="input" type="text">
                </div>
                <div class="form-field">
                    <label for="country_code">Country</label>
                    <input id="country_code" name="country_code" class="input" type="text" placeholder="IN">
                </div>
                <div class="form-field full">
                    <label for="instructions">Delivery Instructions</label>
                    <textarea id="instructions" name="instructions" class="textarea" placeholder="Floor, entry gate, directions"></textarea>
                </div>
                <div class="form-field full">
                    <label style="display:flex; gap:10px; align-items:center;">
                        <input type="checkbox" name="is_default" value="1" id="is_default">
                        <span>Make this my default address</span>
                    </label>
                </div>
            </div>

            <div class="hero-actions">
                <button type="submit" class="btn" id="saveBtn">Save</button>
                <button type="button" class="btn-outline" data-close-modal>Cancel</button>
            </div>
        </form>
    </dialog>
@endsection

@section('scripts')
    <script>
        (function () {
            const modal = document.getElementById('addressModal');
            const form = document.getElementById('addressForm');
            const methodField = document.getElementById('methodField');
            const modalTitle = document.getElementById('modalTitle');

            const openModal = () => {
                if (typeof modal.showModal === 'function') modal.showModal();
                else modal.setAttribute('open', '');
            };
            const closeModal = () => {
                if (typeof modal.close === 'function') modal.close();
                else modal.removeAttribute('open');
            };

            const setValue = (id, value) => {
                const el = document.getElementById(id);
                if (!el) return;
                if (el.type === 'checkbox') el.checked = !!value;
                else el.value = value == null ? '' : String(value);
            };

            const resetForm = () => {
                form.action = "{{ route('frontend.account.addresses.store') }}";
                methodField.value = "POST";
                modalTitle.textContent = "Create Address";
                ['type','label','recipient_name','phone','address_line_1','address_line_2','landmark','city','state','postal_code','country_code','instructions'].forEach((k) => setValue(k, ''));
                setValue('type', 'home');
                setValue('is_default', false);
            };

            document.querySelectorAll('[data-open-create]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    resetForm();
                    openModal();
                });
            });

            document.querySelectorAll('[data-open-edit]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const address = JSON.parse(btn.dataset.address || '{}');
                    resetForm();
                    form.action = "{{ url('/account/addresses') }}/" + address.id;
                    methodField.value = "PUT";
                    modalTitle.textContent = "Edit Address";
                    setValue('type', address.type || 'home');
                    setValue('label', address.label);
                    setValue('recipient_name', address.recipient_name);
                    setValue('phone', address.phone);
                    setValue('address_line_1', address.address_line_1);
                    setValue('address_line_2', address.address_line_2);
                    setValue('landmark', address.landmark);
                    setValue('city', address.city);
                    setValue('state', address.state);
                    setValue('postal_code', address.postal_code);
                    setValue('country_code', address.country_code);
                    setValue('instructions', address.instructions);
                    setValue('is_default', address.is_default);
                    openModal();
                });
            });

            document.querySelectorAll('[data-close-modal]').forEach((btn) => btn.addEventListener('click', closeModal));
            modal.addEventListener('click', (e) => {
                const rect = modal.getBoundingClientRect();
                const inside = rect.top <= e.clientY && e.clientY <= rect.bottom && rect.left <= e.clientX && e.clientX <= rect.right;
                if (!inside) closeModal();
            });
        })();
    </script>
@endsection

