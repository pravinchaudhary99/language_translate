@extends('layouts.header')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ __('lables.users') }}</h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('home') }}" class="text-muted text-hover-primary">{{ __('lables.home') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-200 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-dark">{{ __('lables.users') }} {{ __('lables.list') }}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
                                </svg>
                            </span>
                            <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search user">
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_user">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="black"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black"></rect>
                                </svg>
                            </span>
                            {{ __('lables.add_user') }}</button>
                        </div>
                        <div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered mw-650px">
                                <div class="modal-content">
                                    <div class="modal-header" id="kt_modal_add_user_header">
                                        <h2 class="fw-bolder addUser">{{ __('lables.add_user') }}</h2>
                                        <h2 class="fw-bolder editUser d-none">{{ __('lables.edit_user') }}</h2>
                                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close" data-bs-dismiss="modal">
                                            <span class="svg-icon svg-icon-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                        <form id="add_user_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="#">
                                            <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px" style="max-height: 142px;">
                                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                                    <label class="required fw-bold fs-6 mb-2">{{ __('lables.name') }}</label>
                                                    <input type="text" name="name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Name" data-label="{{ __('lables.name') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                                    <label class="required fw-bold fs-6 mb-2">{{ __('lables.email') }}</label>
                                                    <input type="email" name="email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="example@domain.com" data-label="{{ __('lables.email') }}">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                                    <label class="required fw-bold fs-6 mb-2">{{ __('lables.password') }}</label>
                                                    <input name="password" type="password" class="form-control form-control-solid mb-3 mb-lg-0" data-label="{{ __('lables.password') }}" placeholder="Password">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                                    <label class="required fw-bold fs-6 mb-2">{{ __('lables.password_confirmation') }}</label>
                                                    <input name="password_confirmation" class="form-control form-control-solid mb-3 mb-lg-0" id="confirmPassword" type="password" data-label="{{ __('lables.password_confirmation') }}" placeholder="Password confirmation">
                                                </div>
                                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                                    <label class="required fw-bold fs-6 mb-2">{{ __('lables.role') }}</label>
                                                    <select name="role" id="userRoleSelect" class="form-select form-select-solid mb-3 mb-lg-0" data-label="{{ __('lables.role') }}" data-control="select2" data-dropdown-parent="#kt_modal_add_user">
                                                        <option value="">{{ __('lables.select') }} {{ __('lables.role') }}</option>
                                                        @isset($roles)
                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                            @endforeach
                                                        @endisset
                                                    </select>
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="text-center pt-15">
                                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal" data-kt-users-modal-action="cancel">{{ __('lables.discard') }}</button>
                                                <button type="button" class="btn btn-primary" data-kt-users-modal-action="submit" id="userFormSubmit">
                                                    <span class="indicator-label">{{ __('lables.submit') }}</span>
                                                    <span class="indicator-progress">{{ __('lables.please_wait') }}...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                </button>
                                            </div>
                                        <div></div></form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div id="kt_table_users_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer" id="kt_table_users">
                                <thead>
                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                        <th class="min-w-125px sorting">{{ __('lables.sn') }}</th>
                                        <th class="min-w-125px sorting">{{ __('lables.user') }}</th>
                                        <th class="min-w-125px sorting">{{ __('lables.email') }}</th>
                                        <th class="min-w-125px sorting">{{ __('lables.role') }}</th>
                                        <th class="text-end min-w-100px sorting_disabled">{{ __('lables.action') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/Admin/Users/dataTable.js') }}"></script>
    <script src="{{ asset('assets/js/Admin/Users/validation.js') }}"></script>
@endsection