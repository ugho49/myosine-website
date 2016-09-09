@extends('layouts.private')

@section('style')
    <link rel="stylesheet" href="{{URL::to('/')}}/bower_components/dropzone/dist/min/basic.min.css">
    <link rel="stylesheet" href="{{URL::to('/')}}/bower_components/dropzone/dist/min/dropzone.min.css">

    <style>
        .center-cropped {
            width: 180px;
            height: 180px;
            text-align: center;
            text-align: -webkit-center;
            padding: 2px;
            margin-bottom: 80px;
        }

        .center-cropped a {
            margin-bottom: 40px;
        }

        .center-cropped img {
            height: 100%;
        }
    </style>
@endsection

@section('content')
    <h2>Ajouter des nouvelles photos</h2>
    <div class="row">

        <div class="col-lg-12 text-center">

            <form action="{{ URL::route('admin.photo.upload') }}" method="post" class="dropzone" id="my-dropzone">
                {{ csrf_field() }}
                <div class="dz-default dz-message"><span>Ajouter des nouvelles photos</span></div>
            </form>

        </div>
    </div>

    <hr>

    <h2>Editer les photos existantes</h2>
    <br>
    <div class="row" id="photo_container">
        @foreach($photos as $photo)
            <div class="col-lg-3 col-md-3 col-md-3 col-xs-6" data-name="{{ $photo['name'] }}">
                <div class="center-cropped">
                    <img src="{{ $photo['url'] }}" alt="{{ $photo['name'] }}" class="img-responsive" />
                    <a href="javascript:void(0)" class="btn btn-danger remove_photo" data-name="{{ $photo['name'] }}">Supprimer</a>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('script')
    <script src="{{URL::to('/')}}/bower_components/bootbox.js/bootbox.js"></script>
    <script src="{{URL::to('/')}}/bower_components/dropzone/dist/min/dropzone.min.js"></script>

    <script>
        $(document).ready(function() {

            const base_url = '{{ URL::route('admin.photo.remove', '') }}' + '/';

            //Dropzone.js Options - Upload an image via AJAX.
            Dropzone.options.myDropzone = {
                uploadMultiple: false,
                // previewTemplate: '',
                addRemoveLinks: false,
                //maxFiles: 2,
                dictDefaultMessage: '',
                init: function() {
                    this.on("success", function(file, res) {
                        //console.log('upload success...', res);
                        $('.dz-image-preview').hide();
                        $('.dz-file-preview').hide();

                        add_photo(res.url, res.name);
                    });
                    this.on("queuecomplete", function () {
                        this.removeAllFiles();
                    });
                }
            };

            var myDropzone = new Dropzone("#my-dropzone");

            Dropzone.autoDiscover = false;

            addEventRemove();

            function add_photo (url, name) {
                var main_div = $("<div></div>", {
                    "class": "col-lg-3 col-md-3 col-md-3 col-xs-6",
                    "data-name" : name
                });

                var center_div = $("<div></div>", {"class": "center-cropped"});

                var img = $("<img />", {
                    "src": url,
                    "alt": name,
                    "class": "img-responsive"
                });

                var a = $("<a></a>", {
                    "href": "javascript:void(0)",
                    "class": "btn btn-danger remove_photo",
                    "data-name" : name
                });
                a.text('Supprimer');

                center_div.append(img);
                center_div.append(a);

                main_div.append(center_div);

                main_div.hide().prependTo('#photo_container').fadeIn(700);

                addEventRemove();
            }

            function remove_photo (name) {
                var div = $('div[data-name="'+ name +'"]');
                div.fadeOut(300, function() {
                    $(this).remove();
                });
            }

            function addEventRemove() {
                $('.remove_photo:not("disabled")').click(function(e) {
                    var button = $(this);

                    if (button.attr('disabled')) {
                        return;
                    }

                    button.attr('disabled','disabled');
                    var name = button.data('name');
                    var url = base_url + name;

                    bootbox.setLocale('fr');

                    bootbox.confirm("Etes vous sur ?", function(result) {
                        if (result) {
                            $.get( url , function( data ) {
                                if (data) {
                                    remove_photo(name);
                                } else {
                                    alert('erreur interne intervenue lors de la suppression');
                                    button.removeAttr('disabled');
                                }
                            });
                        }
                        button.removeAttr('disabled');
                    });

                });
            }
        });
    </script>
@endsection