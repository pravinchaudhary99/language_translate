@extends('layouts.header')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ __('lables.roles') }}</h1>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('home') }}" class="text-muted text-hover-primary">{{ __('lables.home') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-200 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-dark">{{ __('lables.roles') }} {{ __('lables.list') }}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="post d-flex flex-column-fluid" id="kt_post">
      <div id="kt_content_container" class="container-xxl">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
          @isset($data['roles'])
            @foreach ($data['roles'] as $role)
              <div class="col-md-4">
                <div class="card card-flush h-md-100">
                  <div class="card-header">
                    <div class="card-title">
                      <h2>{{ $role->name }}</h2>
                    </div>
                  </div>
                  <div class="card-body pt-1">
                    <div class="fw-bolder text-gray-600 mb-5">Total users with this role: {{ $role->users->count() }}</div>
                    <div class="d-flex flex-column text-gray-600">
                      @foreach($role->permissionsList as $index => $permission)
                          @if($index < 7)
                            <div class="d-flex align-items-center py-2">
                                <span class="bullet bg-primary me-3"></span>{{ $permission }}
                            </div>
                          @elseif($index === 7)
                              <div class="d-flex align-items-center py-2">
                                  <span class="bullet bg-primary me-3"></span>
                                  <em>and {{ $role->permissionsList->count() - 7 }} more...</em>
                              </div>
                              @break
                          @endif
                      @endforeach
                    </div>
                  </div>
                  <div class="card-footer flex-wrap pt-0">
                    <a href="" class="btn btn-light btn-active-primary my-1 me-2" data-role="{{ $role->name }}" data-permissions="{{ $role->permissions->pluck('id') }}">View Role</a>
                    <button type="button" class="btn btn-light btn-active-light-primary my-1 editRoleAndPermission" data-id="{{ $role->id }}" data-role="{{ $role->name }}" data-permissions="{{ $role->permissions->pluck('id') }}" data-bs-toggle="modal" data-bs-target="#kt_modal_add_role">Edit Role</button>
                  </div>
                </div>
              </div>
            @endforeach
          @endisset
          <div class="ol-md-4">
            <div class="card h-md-100">
              <div class="card-body d-flex flex-center">
                <button type="button" class="btn btn-clear d-flex flex-column flex-center" data-bs-toggle="modal" data-bs-target="#kt_modal_add_role">
                  <img src="{{ asset('assets/media/illustrations/unitedpalms-1/4.png') }}" alt="" class="mw-100 mh-150px mb-7">
                  <div class="fw-bolder fs-3 text-gray-600 text-hover-primary">Add New Role</div>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="kt_modal_add_role" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered mw-750px">
            <div class="modal-content">
              <div class="modal-header">
                <h2 class="fw-bolder addRole">{{ __('lables.add_a_role') }}</h2>
                <h2 class="fw-bolder editRole d-none">{{ __('lables.edit_a_role') }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-roles-modal-action="close" data-bs-dismiss="modal">
                  <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                      <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
                    </svg>
                  </span>
                </div>
              </div>
              <div class="modal-body scroll-y mx-lg-5 my-7">
                <form id="kt_modal_add_role_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="#">
                  <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_role_header" data-kt-scroll-wrappers="#kt_modal_add_role_scroll" data-kt-scroll-offset="300px" style="max-height: 142px;">
                    <div class="fv-row mb-10 fv-plugins-icon-container">
                      <label class="fs-5 fw-bolder form-label mb-2">
                        <span class="required">Role name</span>
                      </label>
                      <input class="form-control form-control-solid" placeholder="Enter a role name" name="name" data-label="{{ __('lables.role') }}">
                    <div class="fv-plugins-message-container invalid-feedback"></div></div>
                    <div class="fv-row">
                      <label class="fs-5 fw-bolder form-label mb-2">Role Permissions</label>
                      <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                          <tbody class="text-gray-600 fw-bold">
                            @isset($data['permissions'])
                              <tr>
                                <td class="text-gray-800">Administrator Access
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="" data-bs-original-title="Allows a full access to the system" aria-label="Allows a full access to the system"></i></td>
                                <td>
                                  <label class="form-check form-check-custom form-check-solid me-9">
                                    <input class="form-check-input" type="checkbox" value="" id="kt_roles_select_all">
                                    <span class="form-check-label" for="kt_roles_select_all">Select all</span>
                                  </label>
                                </td>
                              </tr>
                              @foreach($data['permissions'] as $key => $permissions)
                                <tr>
                                  <td class="text-gray-800">{{ $key }} {{ __('lables.management') }}</td>
                                  <td>
                                    <div class="d-flex">
                                      @foreach ($permissions as $permission)
                                        <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                          <input class="form-check-input" type="checkbox" value="{{ $permission->id }}" name="permissions[]" data-label="{{ __('lables.permissions') }}">
                                          <span class="form-check-label">{{ ucfirst(\Illuminate\Support\Str::of($permission->name)->afterLast('-')) }}</span>
                                        </label>
                                      @endforeach
                                    </div>
                                  </td>
                                </tr>
                              @endforeach
                            @endisset
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="text-center pt-15">
                    <button type="reset" class="btn btn-light me-3" data-kt-roles-modal-action="cancel" data-bs-dismiss="modal">Discard</button>
                    <button type="buttton" id="rolesAddSubmitButton" class="btn btn-primary" data-kt-roles-modal-action="submit">
                      <span class="indicator-label">Submit</span>
                      <span class="indicator-progress">Please wait...
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
</div>
@endsection

@section('scripts')
  <script src="{{ asset('assets/js/Admin/Roles/validation.js') }}"></script>
@endsection