<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Thoughts</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <h1> TEST ORM </h1>
        @foreach ($categories as $category)
            <h2 style="color: green;"> {{$category->name}} </h2>

            @if (count($category->posts) === 0)
                <p>Sin post :(</p>
            @endif

            @foreach ($category->posts as $post)

                <span style="color:gray"> {{$post->user->name}} </span>
                <h4> {{$post->title}} </h4>
                <p>{{$post->content}}</p>
            @endforeach

        @endforeach
    </body>
</html>
