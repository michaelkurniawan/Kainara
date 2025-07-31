<style>
    /* Because nav wraps .custom-pagination, target directly .custom-pagination ul... */
    .custom-pagination{
        margin-top: 2vh;
    }

    .custom-pagination ul.pagination li.page-item a.page-link {
        font-family: 'AncizarSerif', serif;
        color: #333;
        background-color: #fff;
        border-radius: 10px;
        margin: 0 4px;
        transition: all 0.3s ease-in-out;
    }

    .custom-pagination ul.pagination li.page-item a.page-link:hover {
        background-color: #EAE4D5;
        color: #333;
    }

    .custom-pagination ul.pagination li.page-item.active span.page-link {
        background-color: #AD9D6C;
        border-color: #AD9D6C;
        color: white;
        font-weight: bold;
        border-radius: 10px;
        margin: 0 4px;
    }

    .custom-pagination ul.pagination li.page-item:first-child .page-link,
    .custom-pagination ul.pagination li.page-item:last-child .page-link {
        margin: 0px 3px 0px 3px;
    }
</style>

{{-- Check if paginator has pages before rendering the wrapper --}}
@if ($paginator->hasPages())
    <div class="custom-pagination">
        {{ $paginator->appends(request()->except(['page']))->links('Pagination.pagination') }}
    </div>
@endif