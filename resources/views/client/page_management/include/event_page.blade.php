<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="border p-4 rounded">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="mb-0">{{ $title }}</h5>
                    </div>
                    <hr>
                    <form method="POST" action="{{ route('user.page.save') }}" class="row g-3 ajaxForm" novalidate="novalidate">
                        @csrf
                        <div class="col-md-12">
                            <label for="">Title<span class="text-danger fw-bold">*</span></label>
                            <input type="text" name="title" id="page_title" value="{{ $edit->title ?? "" }}" placeholder="Enter Page Title..."
                                class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label for="description">Description<span class="text-danger fw-bold">*</span></label>
                            <p style=" font-size: 11px; ">Add a description about your product or event</p>
                            <textarea name="description" id="description" class="form-control" cols="10" rows="5" required placeholder="Enter Page Description...">{{ $edit->description ?? "" }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label for="images">Image Gallery<span class="text-danger">*</span></label>
                            <p style="font-size: 11px;">Add images by clicking on this input below</p>
                            <input type="file" name="pages_images[]" id="pages_images" accept="image/*" class="form-control" multiple>
                        </div>
                        <div class="col-md-12">
                            <div id="image-preview-container" class="image-preview-container">
                                @if(isset($edit->galleries) && !empty($edit->galleries))
                                    @foreach($edit->galleries as $index => $image)
                                        <div class="image-preview">
                                            <img src="{{ asset($image->images) }}" alt="Image Preview">
                                            <button class="delete-icon" onclick="removeExistingImage({{ $index }})">&times;</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>


                        {{-- <div id="website_links_container" class="col-md-12"></div> --}}


                        <input type="hidden" id="edit_mode" value="{{ isset($edit->page_website_links) ? '1' : '0' }}">
                        <div id="website_links_container" class="col-md-12">
                            @if(isset($edit) && !empty($edit->page_website_links))
                                @foreach($edit->page_website_links as $index => $link)
                                    <div class="col-md-12 websiteLink" id="link_{{ $index + 1 }}">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-1 text-center">
                                                        <i class="fas fa-link"></i>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <h5>Link {{ $index + 1 }}</h5>
                                                        <div class="form-group">
                                                            <input type="text" name="website_title[]" id="website_titles_{{ $index + 1 }}" value="{{ $link->link_title }}" readonly class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" name="website_link[]" id="website_links_{{ $index + 1 }}" value="{{ $link->website_link }}" readonly class="form-control mt-2">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-right">
                                                        <a href="javascript:void(0);" class="text-danger remove_link_btn float-end" data-link-id="link_{{ $index + 1 }}"><i class="fas fa-trash"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="alert border-0 bg-light-success alert-dismissible fade show py-2">
                            <div class="d-flex align-items-center">
                                <div class="fs-3 text-success"><i class="bi bi-plus-circle-fill add-website-link"></i> &nbsp;<i
                                        class="fa fa-link add-website-link"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="text-success"><a href="javascript:void(0);"
                                            class=" text-success add-website-link">Add Website Link</a></div>
                                </div>
                            </div>
                        </div>

                        {{-- <div id="youtube_links_container" class="col-md-12"></div> --}}

                        <input type="hidden" id="edit_mode" value="{{ isset($edit) ? '1' : '0' }}">
                        <div id="youtube_links_container" class="col-md-12">
                            @if(isset($edit) && !empty($edit->page_youtube_links))
                                @foreach($edit->page_youtube_links as $index => $link)
                                    <div class="col-md-12 youtubeLink" id="youtube_link_{{ $index + 1 }}">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4 text-center">
                                                        <img width="200px" height="150px" src="https://img.youtube.com/vi/{{ parse_url($link->youtube_link, PHP_URL_QUERY) ? explode('=', parse_url($link->youtube_link, PHP_URL_QUERY))[1] : '' }}/0.jpg" alt="Video Thumbnail">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5>Link {{ $index + 1 }}</h5>
                                                        <div class="form-group">
                                                            <input type="text" name="youtube_title[]" id="youtube_titles_{{ $index + 1 }}" value="{{ $link->link_title }}" readonly class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" name="youtube_link[]" id="youtube_links_{{ $index + 1 }}" value="{{ $link->youtube_link }}" readonly class="form-control mt-2">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-right">
                                                        <a href="javascript:void(0);" class="text-danger remove_youtube_link_btn float-end" data-youtube-link-id="youtube_link_{{ $index + 1 }}"><i class="fas fa-trash"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="alert border-0 bg-light-success alert-dismissible fade show py-2">
                            <div class="d-flex align-items-center">
                                <div class="fs-3 text-success"><i class="bi bi-plus-circle-fill add-youtube-video"></i> &nbsp;<i
                                        class="fa-brands fa-youtube add-youtube-video"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="text-success"><a href="javascript:void(0);"
                                            class=" text-success add-youtube-video">Add Youtube Video</a></div>
                                </div>
                            </div>
                        </div>

                        @if (isset($edit->google_maps) && !empty($edit->google_maps))
                            @php
                                $map = explode(', ', $edit->google_maps);
                                $latitude = $map[0];
                                $longitude = $map[1];
                            @endphp
                            <input type="hidden" value="{{ $latitude }}" id="lati">
                            <input type="hidden" value="{{ $longitude }}" id="longi">
                        @endif
                        <input type="hidden" value="{{ $edit->google_maps ?? ""}}" name="google_location" id="google_location">
                        <div id="location_container" style="width: 100%; height: 300px; display: none;" class="col-md-12"></div>


                        <div class="alert border-0 bg-light-success alert-dismissible fade show py-2">
                            <div class="d-flex align-items-center">
                                <div class="fs-3 text-success"><i class="bi bi-plus-circle-fill add-google-map"></i> &nbsp;<i
                                        class="fa-solid fa-map add-google-map"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="text-success"><a href="javascript:void(0);"
                                            class=" text-success add-google-map">Add Google Map</a></div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="edit_id" value="{{ $edit->id ?? ""}}">
                        <div class="form-group mb-3 text-right">
                            <button type="submit" class="btn btn-primary px-5 form-submit-btn col-md-12">{{ isset($edit) ? 'Update' : 'Create' }}
                                Page</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- modal for map  --}}
    <div class="modal fade" id="add-map" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-footer modal-footer-centered">
                    <h3><b>Add Google Map</b></h3>
                    <span>Show a specific location or address on a Google Map</span>
                    <input type="text" name="map" id="location-input" value="" placeholder="Type address here...." class="form-control" required>
                    <div id="map" style="width: 100%; height: 300px;"></div>
                    <button type="submit" class="btn btn-primary px-5 form-submit-btn col-md-12 addLocationBtn">Add To Page</button>
                </div>
            </div>
        </div>
    </div>
    {{-- modal for map end --}}


    {{-- modal for youtube  --}}
    <div class="modal fade" id="add-youtube" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h3><b>Add YouTube Video</b></h3>

                    <div class="alert border-0 bg-light-danger text-danger alert-dismissible fade show alertYoutube"
                        role="alert" style="display: none;">
                        Invalid Youtube Video URL
                    </div>

                    <div class="alert border-0 bg-light-danger text-danger alert-dismissible fade show alertYoutubeInput"
                        role="alert" style="display: none;">
                        Youtube Video URL Required
                    </div>

                    <br>
                    <label>YouTube Link URL <span class="text-danger">*</spancla></label>
                    <input type="text" id="youtube-url" placeholder="Type address here...." class="form-control" required>
                    <br>

                    <div id="video-details" style="display:none;">
                        <p id="video-title"></p>
                        <img id="video-thumbnail" width="460px" height="280px" src="" alt="Video Thumbnail">
                    </div>

                    <button type="submit" class="btn btn-primary px-5 form-submit-btn mt-2 col-md-12" id="fetch-video-details">Add To Page</button>

                </div>
            </div>
        </div>
    </div>
    {{-- modal for youtube end --}}


    {{-- modal for website link  --}}
    <div class="modal fade" id="add-web-link" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">

                    <h3><b>Add Website Link</b></h3>
                    <div class="alert border-0 bg-light-danger text-danger alert-dismissible fade show alertMessage"
                        role="alert" style="display: none;">
                        Website URL & Title Field is Required
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <br>
                    <label>Link URL <span class="text-danger">*</span></label>
                    <input type="url" name="website_url" id="website_url" value="" placeholder="https://"
                        class="form-control" required>

                    <br>

                    <label>Enter Title <span class="text-danger">*</span></label>
                    <input type="text" name="website_title" id="website_title" value=""
                        placeholder="Enter title text for this link" class="form-control" required>

                    <br>
                    <button type="button" class="btn btn-primary px-5 form-submit-btn col-md-12 add_website_link_btn">Add To Page</button>
                </div>
            </div>
        </div>
    </div>
    {{-- modal for website link  --}}

</div>



@section('page-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSnI-NKLzHfh9ksrC9-r0LHuiV4IP-FhE&callback=initMap" async defer></script>
    <script>

        $(document).ready(function() {

            let linkCount = $('#website_links_container .websiteLink').length;

            $(document).on('click', '.add_website_link_btn', function() {

                var websiteUrl = $('#website_url').val();
                var title = $('#website_title').val();

                if (websiteUrl === '' || title === '') {
                    $('.alertMessage').show();
                    setTimeout(function() {
                        $('.alertMessage').hide();
                    }, 3000);
                } else {
                    linkCount++;
                    var newLinkDiv = `
                        <div class="col-md-12 websiteLink" id="link_${linkCount}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 text-center">
                                            <i class="fas fa-link"></i>
                                        </div>
                                        <div class="col-md-9">
                                            <h5>Link ${linkCount}</h5>
                                            <div class="form-group">
                                                <input type="text" name="website_title[]" id="website_titles_${linkCount}" value="${title}" readonly class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="website_link[]" id="website_links_${linkCount}" value="${websiteUrl}" readonly class="form-control mt-2">
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-right">
                                            <a href="javascript:void(0);" class="text-danger remove_link_btn float-end" data-link-id="link_${linkCount}"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#website_links_container').append(newLinkDiv);

                    $('#website_url').val('');
                    $('#title').val('');

                    $('#add-web-link').modal('hide');
                }
            });

            $(document).on('click', '.remove_link_btn', function() {
                var linkId = $(this).data('link-id');
                $('#' + linkId).remove();
                updateLinkNumbers();
            });

            function updateLinkNumbers() {
                linkCount = 0;
                $('.websiteLink').each(function(index) {
                    linkCount++;
                    $(this).attr('id', 'link_' + linkCount);
                    $(this).find('h5').text('Link ' + linkCount);
                    $(this).find('.remove_link_btn').data('link-id', 'link_' + linkCount);
                });
            }

        });

        $(document).ready(function() {

            let youtubeThumbnail = "";
            let youtubeTitle = "";
            let youtubeLink = "";

            $('#youtube-url').on('input', function() {
                var url = $('#youtube-url').val();
                var videoId = url.split('v=')[1];
                if (!videoId) {
                    $('.alertYoutube').show();
                    setTimeout(function() {
                        $('.alertYoutube').hide();
                    }, 3000);
                    return;
                }
                var ampersandPosition = videoId.indexOf('&');
                if (ampersandPosition !== -1) {
                    videoId = videoId.substring(0, ampersandPosition);
                }

                $.ajax({
                    url: 'https://www.youtube.com/oembed?url=' + encodeURIComponent(url) + '&format=json',
                    dataType: 'json',
                    success: function(data) {
                        $('#video-title').text(data.title);
                        $('#video-thumbnail').attr('src', data.thumbnail_url);
                        $('#video-details').show();
                        youtubeThumbnail = data.thumbnail_url;
                        youtubeTitle = data.title;
                        youtubeLink = url;
                    },
                    error: function() {
                        alert("Failed to fetch video details");
                    }
                });
            });

            let youtubeLinkCount = $('#youtube_links_container .youtubeLink').length;

            $('#fetch-video-details').on('click', function() {
                var url = $('#youtube-url').val();
                if (url === "") {
                    $('.alertYoutubeInput').show();
                    setTimeout(function() {
                        $('.alertYoutubeInput').hide();
                    }, 3000);
                    return;
                } else {
                    youtubeLinkCount++;
                    var newLinkDiv = `
                        <div class="col-md-12 youtubeLink" id="youtube_link_${youtubeLinkCount}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 text-center">
                                            <img width="200px" height="150px" src="${youtubeThumbnail}" alt="Video Thumbnail">
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Link ${youtubeLinkCount}</h5>
                                            <div class="form-group">
                                                <input type="text" name="youtube_title[]" id="youtube_titles_${youtubeLinkCount}" value="${youtubeTitle}" readonly class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="youtube_link[]" id="youtube_links_${youtubeLinkCount}" value="${youtubeLink}" readonly class="form-control mt-2">
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-right">
                                            <a href="javascript:void(0);" class="text-danger remove_youtube_link_btn float-end" data-youtube-link-id="youtube_link_${youtubeLinkCount}"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#youtube_links_container').append(newLinkDiv);
                    $('#youtube-url').val('');
                    $('#video-title').text(null);
                    $('#video-thumbnail').attr('src', null);
                    $('#video-details').hide();
                    $('#add-youtube').modal('hide');
                }
            });

            $(document).on('click', '.remove_youtube_link_btn', function() {
                var youtubeLinkId = $(this).data('youtube-link-id');
                $('#' + youtubeLinkId).remove();
                updateYouTubeLinkNumbers();
            });

            function updateYouTubeLinkNumbers() {
                youtubeLinkCount = 0;
                $('.youtubeLink').each(function(index) {
                    youtubeLinkCount++;
                    $(this).attr('id', 'youtube_link_' + youtubeLinkCount);
                    $(this).find('h5').text('Link ' + youtubeLinkCount);
                    $(this).find('.remove_youtube_link_btn').data('youtube-link-id', 'youtube_link_' + youtubeLinkCount);
                });
            }
        });

        $(document).ready(function() {
            const apiKey = 'AIzaSyDSnI-NKLzHfh9ksrC9-r0LHuiV4IP-FhE';
            let maps = {};
            let markers = {};
            let lat, lng;

            if ($("#lati").val() !== "" && $("#longi").val() !== "" && $("#lati").val() !== undefined && $("#longi").val() !== undefined) {
                lat = parseFloat($("#lati").val());
                lng = parseFloat($("#longi").val());
                $('#location_container').show();
                initMap('location_container');
                initMap('map');
                geocodeAddress();
            }

            function initMap(elementId) {
                const map = new google.maps.Map(document.getElementById(elementId), {
                    center: { lat: lat || 0, lng: lng || 0 },
                    zoom: 8,
                });
                const marker = new google.maps.Marker({
                    map: map,
                    position: { lat: lat || 0, lng: lng || 0 },
                    icon: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                });

                maps[elementId] = map;
                markers[elementId] = marker;
            }

            function geocodeAddress() {
                const address = document.getElementById('location-input').value;
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ address: address }, (results, status) => {
                    if (status === 'OK') {
                        const location = results[0].geometry.location;
                        lat = location.lat();
                        lng = location.lng();

                        for (const id in maps) {
                            if (maps.hasOwnProperty(id)) {
                                maps[id].setCenter(location);
                                markers[id].setPosition(location);
                            }
                        }
                    } else {
                        // Handle geocoding error
                        console.error('Geocode was not successful for the following reason:', status);
                    }
                });
            }

            $(document).on('input', '#location-input', function () {
                initMap('map');
                geocodeAddress();
            });

            $(document).on('click', '.addLocationBtn', function () {
                $('#location_container').show();
                initMap('location_container');
                initMap('map');
                geocodeAddress();
                $('#add-map').modal('hide');
                $("#google_location").val(lat + ', ' + lng);
                console.log('Latitude:', lat);
                console.log('Longitude:', lng);
            });

            // Load the Google Maps script dynamically
            $.getScript(`https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initMap`);
        });

        $(document).ready(function() {
            $('#pages_images').on('change', function(event) {
                const files = event.target.files;
                const $container = $('#image-preview-container');
                $container.empty(); // Clear previous previews

                $.each(files, function(index, file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const $div = $('<div>', { class: 'image-preview' });

                        const $img = $('<img>', { src: e.target.result });

                        const $button = $('<button>', {
                            class: 'delete-icon',
                            html: '&times;',
                            click: function() {
                                $div.remove();
                                removeFile(index);
                            }
                        });

                        $div.append($img).append($button);
                        $container.append($div);
                    };

                    reader.readAsDataURL(file);
                });
            });

            function removeFile(index) {
                const input = $('#pages_images')[0];
                const dt = new DataTransfer();

                const { files } = input;
                for (let i = 0; i < files.length; i++) {
                    if (i !== index) {
                        dt.items.add(files[i]);
                    }
                }

                input.files = dt.files;
            }

            function removeExistingImage(index) {
                // Implement logic to remove existing image from the database or update form data
                // For example, update hidden input or make an AJAX request to update database
                // This function assumes you handle the backend logic for deleting the image
                console.log('Removing existing image at index:', index);
                // Optionally, you can remove the preview from the DOM
                // $(`.image-preview:eq(${index})`).remove();
            }
        });

        validations = $(".ajaxForm").validate();
        $('.ajaxForm').submit(function(e) {
            e.preventDefault();
            validations = $(".ajaxForm").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {

            },true);
        });

        $(document).on('click', '.add-youtube-video', function() {
            $('#uploadAttachment').modal('hide');
            $('#add-youtube').modal('show');

        });

        $(document).on('click', '.add-website-link', function() {
            $('#website_url').val(null);
            $('#website_title').val(null);
            $('#add-web-link').modal('show');
        });

        $(document).on('click', '.add-google-map', function(){
            $('#add-map').modal('show');
        });


    </script>
@endsection
