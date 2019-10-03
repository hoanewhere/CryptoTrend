<?php
    $trends = array();

    $trends['aaa'] = ['a'=>'青い空'];
    $trends['aaa'] += ['b'=>'黒いさくらんぼ', 'c'=>'シカト'];
    $trends['bbb'] = ['a'=>'アイウエオ'];
?>

@extends('layouts.crypto')

@section('content')
<p>{{print_r($trends)}}</p>
@endsection