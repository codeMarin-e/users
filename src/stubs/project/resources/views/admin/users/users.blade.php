@pushonceOnReady('below_js_on_ready')
<script>
    //CHANGE FILTER
    $(document).on('change', '.js_filter', function(e) {
        var $this = $(this);
        var $thisVal = $this.val();
        if($thisVal == 'all') {
            window.location.href= $this.attr('data-action_all')
            return;
        }
        window.location.href= $this.attr('data-action').replace('__VAL__', $this.val());
    });
</script>
@endpushonceOnReady

{{-- @HOOK_USERS_SCRIPTS --}}

<x-admin.main>
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route("{$route_namespace}.home")}}"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item active">@lang('admin/users/users.users')</li>
        </ol>

        <div class="row">
            <div class="col-12">
                @can('create', App\Models\User::class)
                    <a href="{{ route("{$route_namespace}.users.create") }}"
                       class="btn btn-sm btn-primary h5"
                       title="create">
                        <i class="fa fa-plus mr-1"></i>@lang('admin/users/users.create')
                    </a>
                @endcan

                <a href="{{route("{$route_namespace}.users.index_xlsx")}}"
                   class="btn btn-sm btn-success h5">@lang('admin/users/users.xlsx')</a>

                {{-- @HOOK_USERS_ADDON_LINKS --}}

            </div>
        </div>

        <form autocomplete="off">
            <div class="row">
                {{-- ROLES--}}
                <div class="form-group row col-lg-3">
                    <label for="filters[role]" class="col-form-label col-sm-2">@lang('admin/users/users.filter_roles'):</label>
                    <div class="col-sm-10">
                        <select id="filters[role]"
                                name="filters[role]"
                                data-action_all="{{marinarFullUrlWithQuery( ['filters' => ['role' => null]] )}}"
                                data-action="{{marinarFullUrlWithQuery( ['filters' => ['role' => '__VAL__']] )}}"
                                class="form-control js_filter">
                            <option value='all'>@lang('admin/users/users.filter_roles_all')</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}"
                                        @isset($filters['role']) @if($filters['role'] === $role->id) selected="selected" @endif @endisset
                                >@lang("admin/marinar.roles.{$role->guard_name}.{$role->name}")[{{$role->guard_name}}]
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- END ROLES--}}

                {{-- @HOOK_USERS_AFTER_ROLES FILTER --}}

                {{-- TYPE --}}
                <div class="form-group row col-lg-3">
                    <label for="filters[type]" class="col-form-label col-sm-2">@lang('admin/users/users.filter_type'):</label>
                    <div class="col-sm-10">
                        <select id="filters[type]"
                                name="filters[type]"
                                data-action_all="{{marinarFullUrlWithQuery( ['filters' => ['type' => null]] )}}"
                                data-action="{{marinarFullUrlWithQuery( ['filters' => ['type' => '__VAL__']] )}}"
                                class="form-control js_filter">
                            <option value='all'>@lang('admin/users/users.filter_type_all')</option>
                            <option value='private'
                                    @isset($filters['type']) @if($filters['type'] == 'private') selected="selected" @endif @endisset
                            >@lang('admin/users/users.filter_type_private')</option>
                            <option value='company'
                                    @isset($filters['type']) @if($filters['type'] == 'company') selected="selected" @endif @endisset
                            >@lang('admin/users/users.filter_type_company')</option>
                        </select>
                    </div>
                </div>
                {{-- END TYPE --}}

                {{-- @HOOK_USERS_AFTER_TYPE_FILTER --}}

                {{-- SEARCH --}}
                <div class="form-group row col-lg-3">
                    <div class="col-sm-10">
                        <div class="input-group">
                            <input type="text"
                                   name="search"
                                   id="search"
                                   placeholder="@lang('admin/users/users.search')"
                                   value="@if(isset($search)){{$search}}@endif"
                                   class="form-control "
                            />
                            <div class="input-group-append">
                                <button class="btn btn-primary"><i class="fas fa-search text-grey"
                                                                   aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END SEARCH --}}

                {{-- @HOOK_USERS_AFTER_SEARCH --}}

            </div>

        </form>

        <x-admin.box_messages />

        <div class="table-responsive rounded ">
            <table class="table table-sm">
                <thead class="thead-light">
                <tr class="">
                    <th scope="col" class="text-center">@lang('admin/users/users.id')</th>
                    {{-- @HOOK_USERS_AFTER_ID_TH --}}
                    <th scope="col" class="w-75">@lang('admin/users/users.name')</th>
                    {{-- @HOOK_USERS_AFTER_NAME_TH --}}
                    <th scope="col" class="text-center">@lang('admin/users/users.edit')</th>
                    {{-- @HOOK_USERS_AFTER_EDIT_TH --}}
                    <th scope="col" class="text-center">@lang('admin/users/users.remove')</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    @php
                        $userEditUri = route("{$route_namespace}.users.edit", $user);
                    @endphp
                    <tr data-id="{{$user->id}}"
                        data-parent="{{$user->parent_id}}"
                        data-show="1">
                        <td scope="row" class="text-center align-middle"><a href="{{ $userEditUri }}"
                                                                            title="@lang('admin/users/users.edit')"
                            >{{ $user->id }}</a></td>

                        {{-- @HOOK_USERS_AFTER_ID --}}

                        {{--    NAME    --}}
                        <td class="w-75 align-middle">
                            <a href="{{ $userEditUri }}"
                               title="{{$user->address->fullname}}"
                               class="@if(!$user->active) text-danger @endif"
                            >{{ \Illuminate\Support\Str::words($user->address->fullname, 40,'...') }}</a></td>

                        {{-- @HOOK_USERS_AFTER_NAME--}}

                        {{--    EDIT    --}}
                        <td class="text-center">
                            <a class="btn btn-link text-success"
                               href="{{ $userEditUri }}"
                               title="@lang('admin/users/users.edit')"><i class="fa fa-edit"></i></a></td>

                        {{-- @HOOK_USERS_AFTER_EDIT --}}

                        {{--    DELETE    --}}
                        <td class="text-center">
                            @can('delete', $user)
                                <form action="{{ route("{$route_namespace}.users.destroy", $user->id) }}"
                                      method="POST"
                                      id="delete[{{$user->id}}]">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link text-danger"
                                            title="@lang('admin/users/users.remove')"
                                            onclick="if(confirm('@lang("admin/users/users.remove_ask")')) document.querySelector( '#delete\\[{{$user->id}}\\] ').submit() "
                                            type="button"><i class="fa fa-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%">@lang('admin/users/users.no_users')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{$users->links('admin.paging')}}

        </div>
    </div>
</x-admin.main>
