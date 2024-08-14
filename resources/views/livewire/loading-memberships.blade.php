<div class="form-inline mx-5">
    <button wire:click="refresh" class="btn btn-warning btn-sm" wire:loading.class="d-none"><i class="fa fa-refresh"></i> Refresh Data</button>
    <div wire:loading>
        <i class="fa fa-refresh fa-spin"></i> Processing ...
    </div>
</div>
