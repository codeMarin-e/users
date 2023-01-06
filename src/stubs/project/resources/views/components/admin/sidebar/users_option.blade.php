{{--   USERS --}}
<li class="nav-item @if(request()->route()->named("{$whereIam}.users.*")) active @endif">
    <a class="nav-link " href="{{route("{$whereIam}.users.index")}}">
        <i class="fa fa-fw fa-users mr-1"></i>
        <span>@lang("admin/users/users.sidebar")</span>
    </a>
</li>
