@extends('panel.layout.app')
@section('title', 'AI Chat')

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
                        class="page-pretitle flex items-center">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ __('AI Chat') }}
                    </h2>
                </div>
                <div class="col">
                    <div class="mt-2 float-right">
                        @if (env('APP_STATUS') == 'Demo')
                            <a onclick="return toastr.info('This feature is disabled in Demo version.')"
                                class="btn btn-success">{{ __('Add Template') }}</a>
                        @else
                            <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.chat.aiChatUpdate')) }}"
                                class="btn btn-success">{{ __('Add Template') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('panel.user.openai_chat.components.list')
@endsection

@section('script')
    <script src="/assets/js/panel/openai_chat.js"></script>
@endsection
