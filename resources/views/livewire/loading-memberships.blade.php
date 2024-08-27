<div class="form-inline mx-5">
    <button type="button" id="refreshButton" class="btn btn-warning btn-sm">
        <i class="fa fa-refresh"></i> Refresh Data
    </button>
    <div id="loadingSpinner" class="d-none">
        <i class="fa fa-refresh fa-spin"></i> Processing ...
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const refreshButton = document.getElementById('refreshButton');
        const loadingSpinner = document.getElementById('loadingSpinner');

        refreshButton.addEventListener('click', () => {
            refreshButton.classList.add('d-none');
            loadingSpinner.classList.remove('d-none');

            const { origin, pathname, search } = new URL(window.location.href);
            window.location.href = `${origin}${pathname}${search}`;
        });
    });
</script>
