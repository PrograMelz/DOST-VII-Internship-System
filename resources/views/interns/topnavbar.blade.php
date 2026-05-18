<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Internship System</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="{{ route('intern.dtr') }}">DTR</a>
            <span class="navbar-text me-3">Welcome, {{ $intern->full_name }}</span>
            <form method="POST" action="{{ route('intern.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>
