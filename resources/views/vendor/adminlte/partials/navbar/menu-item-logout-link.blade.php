<li class="nav-item">
    <form class="d-flex justify-content-center align-items-center nav-link" action="{{route('logout')}}" method="post">
        @csrf
        <x-adminlte-button id="logout" class="btn-sm" label="Logout" type="submit"/>
    </form>
</li>
