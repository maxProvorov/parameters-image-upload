<!DOCTYPE html>
<html>
<head>
    <title>Parameters</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1 class="mt-4">Parameters</h1>
    <form method="GET" action="{{ url('/parameters') }}">
        <input type="text" name="search" placeholder="Поиск по id и title">
        <button type="submit" class="btn btn-primary">search</button>
    </form>
    <div class="list-group">
        @foreach ($parameters as $parameter)
        <div class="list-group-item">
            <h2>{{ $parameter->title }}</h2>
            <h4 class="text-muted">Тип: {{ $parameter->type }}</h4>
            @if ($parameter->type == 2)
                <form method="POST" action="{{ url('/parameters/' . $parameter->id . '/images') }}" enctype="multipart/form-data">
                    @csrf
                    <label for="icon">Иконка</label>
                    <input type="file" name="icon">
                    <label for="icon_gray">Серая иконка</label>
                    <input type="file" name="icon_gray">
                    <button type="submit" class="btn btn-success">Upload</button>
                </form>
                @if ($parameter->images)
                    <div style="display: flex; " class="mb-2"> 
                        <div class="col-md-3">
                        @if ($parameter->images->icon)
                                <img src="{{ url('/storage/' . $parameter->images->icon) }}" class="img-thumbnail mb-2" alt="Icon">
                                <form method="POST" action="{{ url('/parameters/' . $parameter->id . '/images/icon') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                @endif
                            </div>
                            <div class="col-md-3">
                        @if ($parameter->images->icon_gray)
                                <img src="{{ url('/storage/' . $parameter->images->icon_gray) }}" class="img-thumbnail mb-2" alt="Icon Gray">
                                <form method="POST" action="{{ url('/parameters/' . $parameter->id . '/images/icon_gray') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                @endif
                            </div>
                    </div>
                @endif

            @endif
        </div>
        @endforeach
    </div>
</body>
</html>
