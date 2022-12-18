function startTimer(endTime, startTime) {
	var timer = document.getElementById('procent');
	var circle = document.getElementById('circle');
	

    var period = endTime.diff(startTime, 'second');
    var delay = dayjs().diff(startTime, 'second');

    circle.style.strokeDashoffset = 440*(delay/period);
    circle.style.animationDuration  = (period - delay)+"s";

    timer.innerHTML = timeFormat(period - delay);
    
	var acrInterval = setInterval (function() {
		diff = endTime.diff(dayjs(), 'second');
		timer.innerHTML = timeFormat(diff);
		if(diff <= 0.05) {
			circle.style.strokeDashoffset="440";
			timer.innerHTML = "0:00";
			clearInterval(acrInterval);
		};
	}, 10);
}


function timeFormat(secs) {
	if(secs < 0) {
		return "0:00";
	}
	var minutes = 0;
	var seconds = Math.ceil(secs);
	while(seconds >= 60) {
		seconds -= 60;
		minutes ++;
	}
	if(seconds < 10) {
		return (minutes.toString() + ":0" + seconds.toString());
	}
	return (minutes.toString() + ":" + seconds.toString());
}