@php $inputBag = 'users'; @endphp

@pushonce('above_css')
<link href="{{ asset('admin/vendor/chosen1.8.7/bootstrap4.chosen.min.css') }}" rel="stylesheet" type="text/css"/>
@endpushonce

@pushonce('below_js')
<script type="text/javascript" src="{{ asset('admin/vendor/chosen1.8.7/chosen.jquery.min.js') }}"></script>
@endpushonce

@pushonceOnReady('below_js_on_ready')
<script>
    $("#{{$inputBag}}\\[roles\\]\\[\\]").chosen({
        placeholder_text_multiple:  '@lang('admin/users/user.choose_roles')',
        no_results_text:  '@lang('admin/users/user.choose_roles_no_results')',
        width:"100%"
    });

    //COMPANY TYPE CHECKBOX
    $(document).on('change', '#{{$inputBag}}\\[type\\]', function(e) {
        var $this = $(this);
        if($this.is(":checked")) {
            $('#company_fieldset')
                .removeClass('d-none')
                .find("input")
                .removeAttr("disabled");
            return;
        }
        $('#company_fieldset')
            .addClass('d-none')
            .find("input")
            .attr("disabled", "disabled");
    });
</script>
@endpushonceOnReady

@pushonce('below_templates')
@if(isset($chUser) && $authUser->can('delete', $chUser))
    <form action="{{ route("{$route_namespace}.users.destroy", $chUser) }}"
          method="POST"
          id="delete[{{$chUser->id}}]">
        @csrf
        @method('DELETE')
    </form>
@endif
@endpushonce

@isset($chUser)
    @pushonceOnReady('below_js_on_ready')
    <script>
        $('#users2fa').on('hide.bs.collapse', function () {
            $('#users2faBtn').removeClass('btn-warning');
            $('#users2faBtn').addClass('btn-primary');
        });
        $('#users2fa').on('show.bs.collapse	', function () {
            $('.js_usersAddonForm.btn-warning').first().click();
            $('#users2faBtn').removeClass('btn-primary');
            $('#users2faBtn').addClass('btn-warning');
        });
    </script>
    @endpushonceOnReady
    @if (session('status') && in_array(session('status'), ['two-factor-authentication-enabled', 'recovery-codes-generated', 'two-factor-authentication-disabled']))
        @pushonceOnReady('below_js_on_ready')
        <script>
            $("#users2faBtn").click();
        </script>
        @endpushonceOnReady
    @endif
@endisset


{{-- @HOOK_USER_SCRIPTS --}}

<x-admin.main>
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route("{$route_namespace}.home")}}"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route("{$route_namespace}.users.index") }}">@lang('admin/users/users.users')</a></li>
            <li class="breadcrumb-item active">@isset($chUser){{ $chUser->id }}@else @lang('admin/users/user.create') @endisset</li>
        </ol>

        <div class="card">
            <div class="card-body">
                <form action="@isset($chUser){{ route("{$route_namespace}.users.update", $chUser) }}@else{{ route("{$route_namespace}.users.store") }}@endisset"
                      method="POST"
                      autocomplete="off"
                      enctype="multipart/form-data">
                    @csrf
                    @isset($chUser)@method('PATCH')@endisset

                    <h1>Just for the test</h1>

                    <x-admin.box_messages />

                    @foreach($errors->$inputBag->all() as $error)
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissable">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>{{ $error }}</strong>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- @HOOK_USER_BEGINNING --}}

                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[addr][fname]"
                                   class="col-form-label"
                            >@lang('admin/users/user.addr.fname')</label>
                            <input type="text"
                                   name="{{$inputBag}}[addr][fname]"
                                   id="{{$inputBag}}[addr][fname]"
                                   value="{{ old("{$inputBag}.addr.fname", (isset($chUser)? $chUser->address->fname: '')) }}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('addr.fname')) is-invalid @endif"
                            />
                        </div>
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[addr][lname]"
                                   class="col-form-label"
                            >@lang('admin/users/user.addr.lname')</label>
                            <input type="text"
                                   name="{{$inputBag}}[addr][lname]"
                                   id="{{$inputBag}}[addr][lname]"
                                   value="{{ old("{$inputBag}.addr.lname", (isset($chUser)? $chUser->address->lname: '')) }}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('addr.lname')) is-invalid @endif"
                            />
                        </div>
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[addr][email]"
                                   class="col-form-label"
                            >@lang('admin/users/user.addr.email')</label>
                            <input type="text"
                                   name="{{$inputBag}}[addr][email]"
                                   id="{{$inputBag}}[addr][email]"
                                   value="{{ old("{$inputBag}.addr.email", (isset($chUser)? $chUser->address->email: '')) }}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('addr.email')) is-invalid @endif"
                            />
                        </div>
                    </div>

                    @php
                        $isCompany = old("{$inputBag}.type") || (is_null(old("{$inputBag}.type")) && isset($chUser) && $chUser->type == 'COMPANY' );
                    @endphp

                    <div class="form-group row form-check">
                        <div class="col-lg-6">
                            <input type="checkbox"
                                   value="COMPANY"
                                   id="{{$inputBag}}[type]"
                                   name="{{$inputBag}}[type]"
                                   class="form-check-input @if($errors->$inputBag->has('type'))is-invalid @endif"
                                   @if($isCompany)checked="checked"@endif
                            />
                            <label class="form-check-label"
                                   for="{{$inputBag}}[type]">@lang('admin/users/user.type.company')</label>
                        </div>
                    </div>

                    <div class="form-group row @if(!$isCompany) d-none @endif"
                         id="company_fieldset">
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[addr][company]"
                                   class="col-form-label"
                            >@lang('admin/users/user.addr.company')</label>
                            <input type="text"
                                   name="{{$inputBag}}[addr][company]"
                                   id="{{$inputBag}}[addr][company]"
                                   @if(!$isCompany) disabled="disabled" @endif
                                   value="{{ old("{$inputBag}.addr.company", (isset($chUser)? $chUser->address->company: '')) }}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('addr.company')) is-invalid @endif"
                            />
                        </div>
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[addr][orgnum]"
                                   class="col-form-label"
                            >@lang('admin/users/user.addr.orgnum')</label>
                            <input type="text"
                                   name="{{$inputBag}}[addr][orgnum]"
                                   id="{{$inputBag}}[addr][orgnum]"
                                   @if(!$isCompany) disabled="disabled" @endif
                                   value="{{ old("{$inputBag}.addr.orgnum", (isset($chUser)? $chUser->address->orgnum: '')) }}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('addr.orgnum')) is-invalid @endif"
                            />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[addr][phone]"
                                   class="col-form-label"
                            >@lang('admin/users/user.addr.phone')</label>
                            <input type="text"
                                   name="{{$inputBag}}[addr][phone]"
                                   id="{{$inputBag}}[addr][phone]"
                                   value="{{ old("{$inputBag}.addr.phone", (isset($chUser)? $chUser->address->phone: '')) }}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('addr.phone')) is-invalid @endif"
                            />
                        </div>
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[addr][postcode]"
                                   class="col-form-label"
                            >@lang('admin/users/user.addr.postcode')</label>
                            <input type="text"
                                   name="{{$inputBag}}[addr][postcode]"
                                   id="{{$inputBag}}[addr][postcode]"
                                   value="{{ old("{$inputBag}.addr.postcode", (isset($chUser)? $chUser->address->postcode: '')) }}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('addr.postcode')) is-invalid @endif"
                            />
                        </div>
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[addr][city]"
                                   class="col-form-label"
                            >@lang('admin/users/user.addr.city')</label>
                            <input type="text"
                                   name="{{$inputBag}}[addr][city]"
                                   id="{{$inputBag}}[addr][city]"
                                   value="{{ old("{$inputBag}.addr.city", (isset($chUser)? $chUser->address->city: '')) }}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('addr.city')) is-invalid @endif"
                            />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label for="{{$inputBag}}[addr][street]"
                                   class="col-form-label"
                            >@lang('admin/users/user.addr.street')</label>
                            <input type="text"
                                   name="{{$inputBag}}[addr][street]"
                                   id="{{$inputBag}}[addr][street]"
                                   value="{{ old("{$inputBag}.addr.street", (isset($chUser)? $chUser->address->street: '')) }}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('addr.street')) is-invalid @endif"
                            />
                        </div>
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[addr][country]"
                                   class="col-form-label"
                            >@lang('admin/users/user.addr.country')</label>
                            <input type="text"
                                   name="{{$inputBag}}[addr][country]"
                                   id="{{$inputBag}}[addr][country]"
                                   value="{{ old("{$inputBag}.addr.country", (isset($chUser)? $chUser->address->country: '')) }}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('addr.country')) is-invalid @endif"
                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="form-group col-12">
                            @php
                                $oldRoles = old("{$inputBag}.roles",
                                    (isset($chUser)? $chUser->roles->pluck('id')->all() : []));
                            @endphp
                            <label for="{{$inputBag}}[roles][]">@lang('admin/users/user.roles')</label>
                            <select id="{{$inputBag}}[roles][]"
                                    name="{{$inputBag}}[roles][]"
                                    multiple="multiple"
                                    onchange="this.classList.remove('is-invalid')"
                                    class="form-control @if($errors->$inputBag->has("roles")) is-invalid @endif">
                                @foreach($roles as $role)
                                    <option value="{{$role->id}}"
                                            @if(in_array($role->id, $oldRoles)) selected="selected" @endif
                                    >@lang("admin/marinar.roles.{$role->guard_name}.{$role->name}")[{{$role->guard_name}}]</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- @HOOK_USER_AFTER_ROLES --}}

                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[password]"
                                   class="col-form-label"
                            >@lang('admin/users/user.password')</label>
                            <input type="password"
                                   autocomplete="new-password"
                                   name="{{$inputBag}}[password]"
                                   id="{{$inputBag}}[password]"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('password')) is-invalid @endif"
                            />
                        </div>
                        <div class="col-lg-4">
                            <label for="{{$inputBag}}[password_confirmation]"
                                   class="col-form-label"
                            >@lang('admin/users/user.password_confirmation')</label>
                            <input type="password"
                                   autocomplete="new-password"
                                   name="{{$inputBag}}[password_confirmation]"
                                   id="{{$inputBag}}[password_confirmation]"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="form-control @if($errors->$inputBag->has('password_confirmation')) is-invalid @endif"
                            />
                        </div>
                    </div>

                    {{-- @HOOK_USER_AFTER_PASSWORD --}}

                    <div class="form-group row form-check">
                        <div class="col-lg-6">
                            <input type="checkbox"
                                   value="1"
                                   id="{{$inputBag}}[active]"
                                   name="{{$inputBag}}[active]"
                                   class="form-check-input @if($errors->$inputBag->has('active'))is-invalid @endif"
                                   @if(old("{$inputBag}.active") || (is_null(old("{$inputBag}.active")) && isset($chUser) && $chUser->active ))checked="checked"@endif
                            />
                            <label class="form-check-label"
                                   for="{{$inputBag}}[active]">@lang('admin/users/user.active')</label>
                        </div>
                    </div>
                    {{-- @HOOK_USER_CHECKBOXES --}}

                    <div class="form-group row">
                        @isset($chUser)
                            @can('update', $chUser)
                                <button class='btn btn-success mr-2'
                                        type='submit'
                                        name='action'>@lang('admin/users/user.save')</button>

                                <button class='btn btn-info mr-2'
                                        type='submit'
                                        name='update'>@lang('admin/users/user.update')</button>
                            @endcan

                            @can('delete', $chUser)
                                <button class='btn btn-danger mr-2'
                                        type='button'
                                        onclick="if(confirm('@lang("admin/users/user.delete_ask")')) document.querySelector( '#delete\\[{{$chUser->id}}\\] ').submit() "
                                        name='delete'>@lang('admin/users/user.delete')</button>
                            @endcan
                        @else
                            @can('create', App\Models\User::class)
                                <button class='btn btn-success mr-2'
                                        type='submit'
                                        name='create'>@lang('admin/users/user.create')</button>
                            @endcan
                        @endisset
                        <a class='btn btn-warning'
                           href="{{ route("{$route_namespace}.users.index") }}"
                        >@lang('admin/users/user.cancel')</a>
                    </div>

                    <div class="form-group row">
                        @isset($chUser)
                            <button class="btn btn-primary mr-2 js_usersAddonForm"
                                    data-toggle="collapse"
                                    id="users2faBtn"
                                    data-target="#users2fa"
                                    type="button"
                                    role="button"
                                    aria-expanded="false"
                                    aria-controls="users2fa">@lang('admin/users/user.two-factor.button')
                                @if($chUser->two_factor_secret) [@lang('admin/users/user.two-factor.button_on')]@else [@lang('admin/users/user.two-factor.button_off')] @endif</button>
                        @endisset

                        {{-- @HOOK_USER_ADDON_BUTTONS --}}
                    </div>

                </form>

            </div>
        </div>
        @isset($chUser)
            <div class="card card-body collapse mt-2" id="users2fa">
                <div>
                    @csrf
                    @if (session('status') == 'two-factor-authentication-enabled')
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="alert alert-success alert-dismissable">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>@lang('admin/users/user.two-factor.enabled')</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (session('status') == 'recovery-codes-generated')
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="alert alert-success alert-dismissable">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>@lang('admin/users/user.two-factor.regenerated')</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (session('status') == 'two-factor-authentication-disabled')
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="alert alert-danger alert-dismissable">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>@lang('admin/users/user.two-factor.disabled')</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($chUser->two_factor_secret)
                        {{--    RAW SWG    --}}
                        <div class="row">
                            <div class="col-lg-3">
                                {!! $chUser->twoFactorQrCodeSvg() !!}
                            </div>
                            <div class="col-lg-9">
                                @foreach((array) $chUser->recoveryCodes() as $recoveryCode)
                                    <div>{{$recoveryCode}}</div>
                                @endforeach
                            </div>
                        </div>
                        <br />
                        <form action="{{route('admin.users.two-factor.recovery-codes', [$chUser])}}" method="POST"  autocomplete="off">
                            @csrf
                            <div class="form-group row">
                                <button type="submit"
                                        class='btn btn-warning mr-2'
                                        type='submit'
                                        onclick="if(!confirm('@lang('two-factor.sure_ask')')) return false;"
                                >@lang('admin/users/user.two-factor.regenerate')</button>
                            </div>
                        </form>

                        <br />

                        <form action="{{route('admin.users.two-factor.disable', [$chUser])}}" method="POST" autocomplete="off">
                            @csrf
                            @method("DELETE")

                            <div class="form-group row">
                                <button type="submit"
                                        class='btn btn-danger mr-2'
                                        onclick="if(!confirm('@lang('two-factor.sure_ask')')) return false;"
                                >@lang('admin/users/user.two-factor.disable')</button>
                            </div>
                        </form>
                    @else
                        <form action="{{route('admin.users.two-factor.enable', [$chUser])}}" method="POST" autocomplete="off">
                            @csrf
                            <div class="form-group row">
                                <button type="submit"
                                        class='btn btn-success mr-2'
                                        onclick="if(!confirm('@lang('two-factor.sure_ask')')) return false;"
                                >@lang('admin/users/user.two-factor.enable')</button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        @endisset
        {{-- @HOOK_USER_ADDONS --}}
    </div>
</x-admin.main>
