@if ($paginator->hasPages())
    <div class="bg-dark rounded-3 py-3 px-4">  <!-- This gives the dark bar -->
        <nav class="d-flex align-items-center justify-content-between">
            
            <!-- Mobile version (Previous / Next only) -->
            <div class="d-flex justify-content-between flex-fill d-sm-none">
                <ul class="pagination mb-0">
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled"><span class="page-link text-white-50">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link text-white" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @if ($paginator->hasMorePages())
                        <li class="page-item"><a class="page-link text-white" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link text-white-50">Next</span></li>
                    @endif
                </ul>
            </div>

            <!-- Desktop version -->
            <div class="d-none d-sm-flex align-items-center justify-content-between w-100">
                
                <!-- Showing X to Y of Z results -->
                <div>
                    <p class="small text-white mb-0">
                        Showing
                        <span class="fw-semibold">{{ $paginator->firstItem() ?? 0 }}</span>
                        to
                        <span class="fw-semibold">{{ $paginator->lastItem() ?? 0 }}</span>
                        of
                        <span class="fw-semibold">{{ $paginator->total() }}</span>
                        results
                    </p>
                </div>

                <!-- Page numbers -->
                <ul class="pagination mb-0">
                    <!-- Previous -->
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled"><span class="page-link text-white-50">&lsaquo;</span></li>
                    @else
                        <li class="page-item"><a class="page-link text-white" href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a></li>
                    @endif

                    <!-- Numbers + Dots -->
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <li class="page-item disabled"><span class="page-link text-white-50">{{ $element }}</span></li>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active bg-primary border-primary">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item"><a class="page-link text-white" href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    <!-- Next -->
                    @if ($paginator->hasMorePages())
                        <li class="page-item"><a class="page-link text-white" href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link text-white-50">&rsaquo;</span></li>
                    @endif
                </ul>
            </div>
        </nav>
    </div>
@endif