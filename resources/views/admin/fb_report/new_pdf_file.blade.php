<html>
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            <?php echo $summary_data?>

            var options = {
                title: '',
                curveType: 'function',
                legend: { position: 'top' },
                colors: ['#337AEE', '#FF9F41', '#97BE6D', '#EC41A7'],
                series: {
                    0: { targetAxisIndex: 0 }, // Impressions - left axis
                    1: { targetAxisIndex: 1 }, // Clicks - right axis
                    2: { targetAxisIndex: 0 }, // CTR - left axis
                    3: { targetAxisIndex: 0 }  // CPC - left axis
                },
                vAxes: {
                    0: {
                        title: 'Impressions',
                        minValue: 0
                    },
                    1: {
                        title: 'Clicks',
                        minValue: 0
                    }
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('summaryChart'));
            chart.draw(data, options);
        }
    </script>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var female_impressions = Math.round({{$gender_graph->female->impressions ?? 0}});
            var male_impressions = Math.round({{$gender_graph->male->impressions ?? 0}});
            var unknown_impressions = Math.round({{$gender_graph->unknown->impressions ?? 0}});

            var female_clicks = Math.round({{$gender_graph->female->clicks ?? 0}});
            var male_clicks = Math.round({{$gender_graph->male->clicks ?? 0}});
            var unknown_clicks = Math.round({{$gender_graph->unknown->clicks ?? 0}});

            var female_ctr = Math.round({{$gender_graph->female->ctr ?? 0}} * 100); // Round to 2 decimal places for CTR
            var male_ctr = Math.round({{$gender_graph->male->ctr ?? 0}} * 100);
            var unknown_ctr = Math.round({{$gender_graph->unknown->ctr ?? 0}} * 100);

            var female_cpc = Math.round({{$gender_graph->female->cpc ?? 0}} * 100); // Round to 2 decimal places for CPC
            var male_cpc = Math.round({{$gender_graph->male->cpc ?? 0}} * 100);
            var unknown_cpc = Math.round({{$gender_graph->unknown->cpc ?? 0}} * 100);

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Gender');
            data.addColumn('number', 'Impressions');
            data.addColumn('number', 'Clicks');
            data.addColumn('number', 'CTR');
            data.addColumn('number', 'CPC');
            data.addRows([
                ['Female', female_impressions, female_clicks, female_ctr, female_cpc],
                ['Male', male_impressions, male_clicks, male_ctr, male_cpc],
                ['Unknown', unknown_impressions, unknown_clicks, unknown_ctr, unknown_cpc]
            ]);

            var options = {
                title: '',
                curveType: 'function',
                legend: { position: 'top' },
                colors: ["#337AEE", '#FF9F41', '#97BE6D', '#EC41A7'],
                series: {
                    0: { targetAxisIndex: 0 }, // Impressions - left axis
                    1: { targetAxisIndex: 1 }, // Clicks - right axis
                    2: { targetAxisIndex: 0 }, // CTR - left axis
                    3: { targetAxisIndex: 0 }  // CPC - left axis
                },
                vAxes: {
                    0: {
                        title: 'Impressions',
                        minValue: 0
                    },
                    1: {
                        title: 'Clicks',
                        minValue: 0
                    }
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('gender-chart'));
            chart.draw(data, options);
        }
    </script>


    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
    
        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Age');
            data.addColumn('number', 'Impressions');
            data.addColumn('number', 'Clicks');
            data.addColumn('number', 'CTR');
            data.addColumn('number', 'CPC');
            data.addRows([
                ['18-24', <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['impressions']) : '0'; ?>, <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['clicks']) : 0 ?>, <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['ctr']) : '0'; ?>, <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['cpc']) : '0'; ?>],
                ['25-34', <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['impressions']) : '0'; ?>, <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['clicks']) : 0 ?>, <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['ctr']) : '0'; ?>, <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['cpc']) : '0'; ?>],
                ['35-44', <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['impressions']) : '0'; ?>, <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['clicks']) : 0 ?>, <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['ctr']) : '0'; ?>, <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['cpc']) : '0'; ?>],
                ['45-54', <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['impressions']) : '0'; ?>, <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['clicks']) : 0 ?>, <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['ctr']) : '0'; ?>, <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['cpc']) : '0'; ?>],
                ['55-64', <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['impressions']) : '0'; ?>, <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['clicks']) : 0 ?>, <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['ctr']) : '0'; ?>, <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['cpc']) : '0'; ?>],
                ['65+', <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['impressions']) : '0'; ?>, <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['clicks']) : 0 ?>, <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['ctr']) : '0'; ?>, <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['cpc']) : '0'; ?>]
            ]);
    
            var options = {
                title: '',
                curveType: 'function',
                legend: { position: 'top' },
                colors: ["#337AEE", '#FF9F41', '#97BE6D', '#EC41A7'],
                series: {
                    0: { targetAxisIndex: 0 }, // Impressions - left axis
                    1: { targetAxisIndex: 1 }, // Clicks - right axis
                    2: { targetAxisIndex: 2 }, // CTR - left axis
                    3: { targetAxisIndex: 3 }  // CPC - left axis
                },
                vAxes: {
                    0: {
                        title: 'Impressions',
                        minValue: 0
                    },
                    1: {
                        title: 'Clicks',
                        minValue: 0
                    }
                }
            };
    
            var chart = new google.visualization.LineChart(document.getElementById('age-chart'));
            chart.draw(data, options);
        }
    </script>

</head>
<body>
  <div id="summaryChart"></div>
  <br>

  <div id="gender-chart"></div>
  <br>
  <div id="age-chart"></div>
  <br>
</body>
</html>
