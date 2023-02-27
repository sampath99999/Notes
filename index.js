$(document).ready(async function () {
    var audio = $("<audio>");
    $("body").append(audio);

    let songs = [];
    await $.ajax({
        url: "api.php",
        type: "POST",
        data: { type: "get" },
        async: true,
        datatype: "json",
        success: function (response) {
            response = JSON.parse(response);
            if (response.length > 0) {
                songs = response;
            } else {
                alert("No songs found");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            return [];
        },
    });
    let tempSongs = [...songs];
    let currentSong = {};

    $("#play").click(async function () {
        if (!currentSong.name && songs.length > 0) {
            await selectSongAndPlay();
            return;
        }
        audio.get(0).pause();
        audio.get(0).currentTime = 0;
        audio.get(0).play();
    });

    $("#next").click(async function () {
        if (!currentSong.name) {
            return;
        }
        if (songs.length > 0) {
            await selectSongAndPlay();
            audio.get(0).pause();
            audio.get(0).currentTime = 0;
            audio.get(0).play();
            $("#name").text("-");
            return;
        }
    });

    $("#reveal").click(function () {
        if (currentSong.name) {
            $("#name").text(currentSong.name);
        }
    });

    audio.on("timeupdate", function () {
        var currentTime = audio.get(0).currentTime;
        var duration = audio.get(0).duration;
        var progress = Math.floor((currentTime / duration) * 100);
        $("#sliderInside").width(progress + "%");
    });

    async function selectSongAndPlay() {
        console.log(tempSongs);
        if (tempSongs.length === 0) {
            tempSongs = [...songs];
        }
        var index = Math.floor(Math.random() * tempSongs.length);
        var song = tempSongs.splice(index, 1)[0];
        currentSong = song;
        audio.attr("src", song.path);
        audio.get(0).play();
    }

    $("#upload").click(function () {
        let name = $("#nameInput").val();
        var fileInput = $("#formFile");
        var files = fileInput.prop("files");
        if (!name || !files.length) {
            alert("Please select file and Enter name");
            return;
        }
        var formData = new FormData();
        formData.append("name", name);
        formData.append("type", "create");
        formData.append("file", files[0]);
        $.ajax({
            url: "api.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.status) {
                    $("#nameInput").val("");
                    $("#formFile").val("");
                    $("#closeModal").click();
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                // Handle the error
                console.log(xhr.responseText);
            },
        });
    });
});
