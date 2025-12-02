function renderChart(data) {
    if(!data || data.length === 0) {
        return;
    }
    const labels = data.map(item => item.date);
    const downloads = data.map(item => item.downloads);
    const totalSize = data.map(item => item.total_size / (1024 * 1024 * 1024)); // MB

    new Chart(document.getElementById("dailyChart"), {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Lượt tải",
                    data: downloads,
                    backgroundColor: "rgba(75, 192, 192, 0.6)",
                    yAxisID: "y1",
                },
                {
                    label: "Dung lượng tải (GB)",
                    data: totalSize,
                    backgroundColor: "rgba(255, 99, 132, 0.6)",
                    yAxisID: "y2",
                }
            ]
        },
        options: {
            scales: {
                y1: { type: "linear", position: "left", beginAtZero: true },
                y2: { type: "linear", position: "right", beginAtZero: true }
            }
        }
    });
}

function renderChartUpload(data) {
    if(!data || data.length === 0) {
        return;
    }
    const labels = data.map(item => item.date);
    const count_file = data.map(item => item.count_file);
    const totalSize = data.map(item => item.total_size / (1024 * 1024 * 1024)); // MB

    new Chart(document.getElementById("dailyUploadChart"), {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Số file",
                    data: count_file,
                    backgroundColor: "rgba(75, 192, 192, 0.6)",
                    yAxisID: "y1",
                },
                {
                    label: "Dung lượng Up",
                    data: totalSize,
                    backgroundColor: "rgba(255, 99, 132, 0.6)",
                    yAxisID: "y2",
                }
            ]
        },
        options: {
            scales: {
                y1: { type: "linear", position: "left", beginAtZero: true },
                y2: { type: "linear", position: "right", beginAtZero: true }
            }
        }
    });
}

function renderChartNewUsers(data) {
    if(!data || data.length === 0) {
        return;
    }
    const labels = data.map(item => item.date);
    const counts = data.map(item => item.counts);
    new Chart(document.getElementById("dailyNewUserChart"), {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Số User",
                    data: counts,
                    backgroundColor: "rgba(75, 192, 192, 0.6)",
                    yAxisID: "y2",
                },
            ]
        },
        options: {
            scales: {
                y2: { type: "linear", position: "right", beginAtZero: true }
            }
        }
    });
}

function renderMonthChart(data) {
    if(!data || data.length === 0) {
        return;
    }
    const labels = data.map(item => "Tháng " + item.month);
    const downloads = data.map(item => item.downloads);
    const totalSize = data.map(item => item.total_size / (1024 * 1024 * 1024)); // MB

    new Chart(document.getElementById("monthChart"), {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Lượt tải",
                    data: downloads,
                    borderColor: "blue",
                    backgroundColor: "rgba(0, 0, 255, 0.2)",
                    yAxisID: "y1",
                },
                {
                    label: "Dung lượng tải (GB)",
                    data: totalSize,
                    borderColor: "red",
                    backgroundColor: "rgba(255, 0, 0, 0.2)",
                    yAxisID: "y2",
                }
            ]
        },
        options: {
            scales: {
                y1: { type: "linear", position: "left", beginAtZero: true },
                y2: { type: "linear", position: "right", beginAtZero: true }
            }
        }
    });

}

function renderWeeklyChart(data) {
    if(!data || data.length === 0) {
        return;
    }
    const labels = data.map(item => "Tuần " + item.week);
    const downloads = data.map(item => item.downloads);
    const totalSize = data.map(item => item.total_size / (1024 * 1024 * 1024)); // MB

    new Chart(document.getElementById("weeklyChart"), {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Lượt tải",
                    data: downloads,
                    borderColor: "blue",
                    backgroundColor: "rgba(0, 0, 255, 0.2)",
                    yAxisID: "y1",
                },
                {
                    label: "Dung lượng tải (GB)",
                    data: totalSize,
                    borderColor: "red",
                    backgroundColor: "rgba(255, 0, 0, 0.2)",
                    yAxisID: "y2",
                }
            ]
        },
        options: {
            scales: {
                y1: { type: "linear", position: "left", beginAtZero: true },
                y2: { type: "linear", position: "right", beginAtZero: true }
            }
        }
    });

}



function renderChartNewNode(data) {
    if(!data || data.length === 0) {
        return;
    }
    const labels = data.map(item => item.date);
    const counts = data.map(item => item.counts);
    new Chart(document.getElementById("dailyMyTreeNode"), {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Số User",
                    data: counts,
                    backgroundColor: "rgba(75, 192, 192, 0.6)",
                    yAxisID: "y2",
                },
            ]
        },
        options: {
            scales: {
                y2: { type: "linear", position: "right", beginAtZero: true }
            }
        }
    });
}
