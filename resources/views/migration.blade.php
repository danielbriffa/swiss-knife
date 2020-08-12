@extends('layout.main')


@section('content')

<section class="section--container section--migration">

    <div class="section--row">
        <div class="section--col">
            <span>Please insert your sql, CREATE or INSERT statement below</span>
        </div>
    </div>
    <div class="section--row flex-grow-1">
        <div class="section--col">
            <form method="POST" action="/migration-execute" class="h-100">
                @csrf
            
                <textarea id="sql" name="sql" class="h-75 w-100">
                </textarea>

                <button id="submit" type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

</section>

@endsection