@if ($paginator->hasPages())
<div class="row">
    <div class="pagination-wrapper">
        <div class="td-pagination d-flex justify-content-center">
            <nav>
                <ul>
                    @if (!$paginator->onFirstPage())
                        <li>
                            <a href="{{ $paginator->previousPageUrl() }}">
                                <svg width="11" height="18" viewBox="0 0 11 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.5211 2.57896L3.78655 9.01845L10.5211 15.4579C11.1156 16.0465 11.107 16.9821 10.5019 17.5607C9.89683 18.1393 8.91842 18.1474 8.30288 17.5789L0.459298 10.0789C-0.1531 9.4932 -0.1531 8.5437 0.459298 7.95795L8.30288 0.45797C8.69675 0.0680351 9.28 -0.0883503 9.82847 0.0489235C10.3769 0.186195 10.8053 0.595762 10.9488 1.1202C11.0924 1.64465 10.9288 2.20235 10.5211 2.57896Z" fill="#130804"></path>
                                </svg>
                            </a>
                        </li>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                <li>
                                    <a class="current">{{ $page }}</a>
                                </li>
                                @else
                                <li>
                                    <a href="{{ $url }}">{{ $page }}</a>
                                </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <li>
                            <a href="{{ $paginator->nextPageUrl() }}">
                                <svg width="11" height="18" viewBox="0 0 11 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.47895 2.57896L7.21345 9.01845L0.47895 15.4579C-0.11555 16.0465 -0.107048 16.9821 0.498059 17.5607C1.10317 18.1393 2.08158 18.1474 2.69712 17.5789L10.5407 10.0789C11.1531 9.4932 11.1531 8.5437 10.5407 7.95795L2.69712 0.45797C2.30325 0.0680351 1.72 -0.0883503 1.17153 0.0489235C0.623057 0.186195 0.194727 0.595762 0.051165 1.1202C-0.0923968 1.64465 0.0711514 2.20235 0.47895 2.57896Z" fill="#130804"></path>
                                </svg>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>
@endif
