<!-- Left Side Of Navbar -->
@foreach([
    ['link' => '/',      'label' => 'Home',  'slug' => 'home'],
    ['link' => '/shows', 'label' => 'Shows', 'slug' => 'shows'],
] as $link)
<li {{isset($activelink) && $activelink == $link['slug'] ? 'class=active' : ''}}><a href="{{$link['link']}}">{{$link['label']}}</a></li>
@endforeach