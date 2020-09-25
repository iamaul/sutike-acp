<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 0.0.1
    </div>
    <strong>Copyright &copy; {{ date('Y') }} <a href="">{{ __app_name() }}</a>.</strong>
    <span class="hidden-xs">All rights reserved.</span>
</footer>
<div class="button-to-top">
    <a href="#" id="back-to-top">
      <i class="material-icons">arrow_upward</i>
    </a>
</div>
@push('js')
<script>
  const idle = new idleJs({
    idle: 7200000, // idle time in ms
    events: ['mousemove', 'keydown', 'mousedown', 'touchstart'], // events that will trigger the idle resetter
    onIdle: function () {
      console.log('idle');
    }, // callback function to be executed after idle time
    onActive: function () {}, // callback function to be executed after back form idleness
    onHide: function () {}, // callback function to be executed when window become hidden
    onShow: function () {}, // callback function to be executed when window become visible
    keepTracking: true, // set it to false of you want to track only once
    startAtIdle: false // set it to true if you want to start in the idle state
  }).start();

  //JX-NOTED realtime date server for default date
  var timestamp = '<?=time();?>';
  var currentDate;
  var firstDate = 0;
  
  function updateTime() {
    timestamp++;
    currentDate = new Date((timestamp+1)*1000);
    $('.default-time-show').html('<b>'+
      currentDate.getFullYear()+'-'+('0'+(currentDate.getMonth()+1)).slice(-2)+'-'+('0'+currentDate.getDate()).slice(-2)+' '+
      ('0'+currentDate.getHours()).slice(-2)+':'+('0'+currentDate.getMinutes()).slice(-2)+':'+('0'+currentDate.getSeconds()).slice(-2)
      +'</b>');
    }
    $(function() {
      setInterval(updateTime, 1000);
  });
  //JX-NOTED-END realtime date server for default date
</script>
@endpush