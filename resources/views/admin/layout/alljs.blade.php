<script src="{{ asset('admin/assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/feather.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/moment.min.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('admin/assets/plugins/@simonwep/pickr/pickr.es5.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/theme-colorpicker.js') }}"></script>
<script src="{{ asset('admin/assets/js/script.js') }}"></script>
<script>
    (function () {
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
            window.jQuery('.js-filter-select').each(function () {
                const $select = window.jQuery(this);
                const optionCount = $select.find('option').length;

                $select.select2({
                    width: '100%',
                    allowClear: false,
                    minimumResultsForSearch: optionCount < 10 ? Infinity : 0
                });
            });
        }

        const currentPath = window.location.pathname.replace(/\/+$/, '');
        document.querySelectorAll('#sidebar-menu a[href]').forEach((anchor) => {
            const href = anchor.getAttribute('href');
            if (!href || href.startsWith('javascript')) {
                return;
            }
            try {
                const linkPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, '');
                if (linkPath && currentPath.startsWith(linkPath)) {
                    anchor.classList.add('active');
                    const li = anchor.closest('li');
                    if (li) {
                        li.classList.add('active');
                    }
                }
            } catch (e) {
            }
        });

        const bindSinglePreview = (input) => {
            const targetSelector = input.dataset.previewSingle;
            if (!targetSelector) {
                return;
            }

            const previewImage = document.querySelector(targetSelector);
            if (!previewImage) {
                return;
            }

            const emptySelector = input.dataset.previewEmpty;
            const emptyNode = emptySelector ? document.querySelector(emptySelector) : null;

            input.addEventListener('change', (event) => {
                const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
                if (!file) {
                    return;
                }

                const objectUrl = URL.createObjectURL(file);
                previewImage.src = objectUrl;
                previewImage.style.display = 'block';
                if (emptyNode) {
                    emptyNode.style.display = 'none';
                }
            });
        };

        const bindMultiplePreview = (input) => {
            const containerSelector = input.dataset.previewMultiple;
            if (!containerSelector) {
                return;
            }

            const container = document.querySelector(containerSelector);
            if (!container) {
                return;
            }

            input.addEventListener('change', (event) => {
                const files = Array.from(event.target.files || []);
                container.innerHTML = '';

                files.forEach((file) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'col-md-2 col-6';

                    const image = document.createElement('img');
                    image.src = URL.createObjectURL(file);
                    image.alt = file.name;
                    image.style.width = '100%';
                    image.style.height = '90px';
                    image.style.objectFit = 'cover';
                    image.style.borderRadius = '6px';
                    image.className = 'border p-1';

                    wrapper.appendChild(image);
                    container.appendChild(wrapper);
                });
            });
        };

        document.querySelectorAll('input[type="file"]').forEach((input) => {
            bindSinglePreview(input);
            bindMultiplePreview(input);
        });

        document.querySelectorAll('form.js-confirm-action').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();

                const title = form.dataset.confirmTitle || 'Are you sure?';
                const text = form.dataset.confirmText || 'Please confirm to continue.';
                const confirmButtonText = form.dataset.confirmButton || 'Yes, continue';

                if (!window.Swal) {
                    form.submit();
                    return;
                }

                window.Swal.fire({
                    title,
                    text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText,
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    })();
</script>

