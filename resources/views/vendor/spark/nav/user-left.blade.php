<!-- Left Side Of Navbar -->
@foreach([
    ['link' => '/',                 'label' => 'Home',              'slug' => 'home'],
    ['link' => '/shows',            'label' => 'Shows',             'slug' => 'shows'],
    ['link' => '/recommendations',  'label' => 'Recommend',         'slug' => 'recommendations'],
    ['link' => '/connections',      'label' => 'Connect',           'slug' => 'connections'],
    ['link' => '/help',             'label' => 'Help',              'slug' => 'help'],
] as $link)
<li {{isset($activelink) && $activelink == $link['slug'] ? 'class=active' : ''}}><a href="{{$link['link']}}">{{$link['label']}}</a></li>
@endforeach