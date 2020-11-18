<!--    Bootstrap   -->
<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
<h1 class="bg-success">{{$title}}</h1>
<ol>
    @foreach($test as $item)
        <li>{{$item}}</li>
    @endforeach
</ol>