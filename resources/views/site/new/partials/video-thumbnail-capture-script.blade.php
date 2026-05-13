<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.testimonial-video-thumb[data-video-thumbnail]').forEach(function(img) {
        var videoUrl = img.getAttribute('data-video-thumbnail');
        if (!videoUrl) return;
        var video = document.createElement('video');
        video.muted = true;
        video.playsInline = true;
        video.preload = 'metadata';
        video.addEventListener('loadedmetadata', function() {
            var duration = video.duration;
            if (!duration || !isFinite(duration)) {
                video.currentTime = 0.5;
            } else {
                var minT = Math.max(0, duration * 0.1);
                var maxT = Math.max(minT, duration * 0.9);
                video.currentTime = minT + Math.random() * (maxT - minT);
            }
        });
        video.addEventListener('seeked', function() {
            try {
                var canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                if (canvas.width && canvas.height) {
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0);
                    img.src = canvas.toDataURL('image/jpeg', 0.85);
                }
            } catch (e) {}
            video.remove();
        });
        video.addEventListener('error', function() { video.remove(); });
        video.src = videoUrl;
        video.load();
    });
});
</script>
