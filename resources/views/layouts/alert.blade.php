@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible show fade">
        <div class="alert-body">
            <button class="close" data-dismiss="alert" aria-label="Tutup"><span>×</span></button>
            {{ $message }}
        </div>
    </div>
@endif
@if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible show fade">
        <div class="alert-body">
            <button class="close" data-dismiss="alert" aria-label="Tutup"><span>×</span></button>
            {{ $message }}
        </div>
    </div>
@endif
