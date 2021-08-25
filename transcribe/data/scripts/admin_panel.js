
$(document).ready(function () {

    /* Constants */
    // const filesURL = "../../api/v1/files/chart";
    // const srqURL = "../../api/v1/stt/chart";
    const statsURL = "../../api/v1/admin/statistics";
    const colorsArray = [
        "#1abc9c","#2ecc71","#3498db","#9b59b6","#34495e",
        "#f39c12","#d35400","#c0392b","#bdc3c7","#7f8c8d",
        "#fdcb6e","#e17055",
    ];

    /* Canavas */
    var filesCanava = document.getElementById('filesChart').getContext('2d');
    var srqCanava = document.getElementById('srqChart').getContext('2d');

    $.ajax({
        type: 'GET',
        method: 'GET',
        url: statsURL,
        async: false,
    }).done(function (response) {
        createPie(filesCanava, response.files_chart.labels, response.files_chart.data);
        createPie(srqCanava, response.sr_chart.labels, response.sr_chart.data);

        $("#totalFiles").html(response.files_count);
        $("#totalOrgs").html(response.org_count);
        $("#totalSysAccess").html(response.sys_org_access_count + " / " + response.org_count);
    }).fail(function(xhr, status, err){

        alert("failed to retrieve stats check console for errors");
        console.log(`${status} | ${err}`)

    });

    /* Functions */
    function createPie(canava, labelsArr, dataArr)
    {
        new Chart(canava, {
            type: 'pie',
            data: {
                labels: labelsArr,
                datasets: [{
                    label: 'Files',
                    data: dataArr,
                    // backgroundColor: [
                    //     'rgb(255, 99, 132)',
                    //     'rgb(54, 162, 235)',
                    //     'rgb(255, 205, 86)'
                    // ],
                    backgroundColor: colorsArray.slice(0, dataArr.length),
                    hoverOffset: 8
                }]
            },
            options: {
                layout: {
                    padding: 15
                },
                /*plugins: {
                    title: {
                        display: true,
                        text: 'Custom Chart Title'
                    }
                },*/
                /*scales: {
                    y: {
                        beginAtZero: true
                    }
                }*/
                tooltips: {
                    mode: 'index'
                }
            }
        });
    }
});

/*
*
* @ https://flatuicolors.com/
    "#25CCF7","#FD7272","#54a0ff","#00d2d3",
    "#1abc9c","#2ecc71","#3498db","#9b59b6","#34495e",
    "#16a085","#27ae60","#2980b9","#8e44ad","#2c3e50",
    "#f1c40f","#e67e22","#e74c3c","#ecf0f1","#95a5a6",
    "#f39c12","#d35400","#c0392b","#bdc3c7","#7f8c8d",
    "#55efc4","#81ecec","#74b9ff","#a29bfe","#dfe6e9",
    "#00b894","#00cec9","#0984e3","#6c5ce7","#ffeaa7",
    "#fab1a0","#ff7675","#fd79a8","#fdcb6e","#e17055",
    "#d63031","#feca57","#5f27cd","#54a0ff","#01a3a4"
*
* */