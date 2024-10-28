<li class="nav-item">
    <form class="nav-link" action="{{route('logout')}}" method="post">
        @csrf
        <x-adminlte-button id="logout" class="btn-sm" label="Logout" type="submit"/>
    </form>
</li>
