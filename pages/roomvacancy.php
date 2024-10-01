<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>


    <script>
        const minuteconvertion = (time) => {
            time = time.toString().split('');
            let partial = time[time.length - 2] + time[time.length - 1];
            let stringednum = '';
            if (partial === '30') {
                partial = '50';
            } else {
                partial = '00';
            }
            for (let i = 0; i < time.length - 2; i++) {
                stringednum += time[i];
            }
            let timeresult = stringednum + partial;
            return timeresult;
        }

        const timedifference = (end, start) => {
            return minuteconvertion(end) - minuteconvertion(start);
        }

        const timeconvertion = (time) => {
            if (time >= 11200 && time <= 11259) {
                time -= 1200;
            } else if (time >= 20100 && time <= 21159) {
                time -= 10000;
                time += 1200;
            } else if (time >= 21200 && time < 21259) {
                time -= 10000
            }
            return time;
        }
    </script>
</body>

</html>