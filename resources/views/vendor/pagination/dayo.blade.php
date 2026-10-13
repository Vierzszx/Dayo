{{-- Paginasi bergaya Dayo (tanpa Tailwind). Dipanggil: $items->links('vendor.pagination.dayo') --}}
@if ($paginator->hasPages())
    <nav class="paginasi" role="navigation" aria-label="Paginasi">
        @if ($paginator->onFirstPage())
            <span class="btn btn--garis btn--kecil btn--nonaktif">Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn--garis btn--kecil">Sebelumnya</a>
        @endif
        <span class="teks-lembut">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn--garis btn--kecil">Berikutnya</a>
        @else
            <span class="btn btn--garis btn--kecil btn--nonaktif">Berikutnya</span>
        @endif
    </nav>
@endif
