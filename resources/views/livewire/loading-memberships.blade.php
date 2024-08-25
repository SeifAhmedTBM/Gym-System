<div class="form-inline mx-5">
    <button type="button" id="refreshButton" class="btn btn-warning btn-sm">
        <i class="fa fa-refresh"></i> Refresh Data
    </button>
    <div id="loadingSpinner" class="d-none">
        <i class="fa fa-refresh fa-spin"></i> Processing ...
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const refreshButton = document.getElementById('refreshButton');
        const loadingSpinner = document.getElementById('loadingSpinner');

        refreshButton.addEventListener('click', function () {
            refreshButton.classList.add('d-none');
            loadingSpinner.classList.remove('d-none');
            setTimeout(function () {
                const currentUrl = new URL(window.location.href);
                const queryParams = currentUrl.searchParams.toString();
                if (queryParams) {
                    window.location.href = `${currentUrl.origin}${currentUrl.pathname}?${queryParams}`;
                } else {
                    window.location.href = `${currentUrl.origin}${currentUrl.pathname}`;
                }
            }, 100);
        });
    });
</script>
