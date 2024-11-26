<footer class="auth-footer">
    <p class="copyrights">تمونوا 2024 ©</p>
</footer>

<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/swiper-bundle.min.js"></script>
<script src="js/select2.min.js"></script>
<script src="js/fancybox.umd.js"></script>
<script src="js/intlTelInput.min.js"></script>
<script src="js/flatpicker.min.js"></script>
<script src="js/ar.js"></script>
<script src="js/stepper.min.js"></script>
<script src="js/lazyload.js"></script>
<script src="js/main.js"></script>
<script src="js/svg.js"></script>

<script>
    function uploadFile(input) {
        var fileName = input.files[0].name;
        var today = new Date();
        var date =
            today.getFullYear() +
            "/" +
            (today.getMonth() + 1) +
            "/" +
            today.getDate();
        var time = today.getHours() + ":" + today.getMinutes();
        var ele = `<div class="file-item">
    <div class="file-info">
      <h5 class="file-name">
        <a href="#!" download>
          ${fileName}
        </a>
      </h5>
      <span class="file-date">
        تاريخ الإنشاء: ${date} - ${time}
      </span>
    </div>
    <div class="file-tools">
      <a href="#!" download class="file-btn">
        <i class="fa-light fa-download"></i>
      </a>
      <button
        type="button"
        class="file-btn"
        onclick="$(this).parents('.file-item').remove()"
      >
        <i class="fa-light fa-trash-can"></i>
      </button>
    </div>
  </div>`;
        $(".files-list").append(ele);
    }
</script>
@stack('js')
