@if ($paginator->hasPages())
    <div class="row">
        <div class="pagination-wrapper d-flex justify-content-center mt-50">
            <div class="td-pagination">
                <nav>
                    <ul>
                        @if ($paginator->onFirstPage())
                            <li><span class="disabled"><i class="icon-arrow-left-2"></i></span></li>
                        @else
                            <li><a href="{{ $paginator->previousPageUrl() }}"><i class="icon-arrow-left-2"></i></a></li>
                        @endif

                        @foreach ($elements as $element)
                            @if (is_string($element))
                                <li class="disabled"><span>{{ $element }}</span></li>
                            @endif

                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <li><span class="current">{{ $page }}</span></li>
                                    @else
                                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        @if ($paginator->hasMorePages())
                            <li><a href="{{ $paginator->nextPageUrl() }}"><i class="icon-arrow-right-3"></i></a></li>
                        @else
                            <li><span class="disabled"><i class="icon-arrow-right-3"></i></span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endif
