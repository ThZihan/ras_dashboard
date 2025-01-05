let parameterRanges = {};

async function fetchRanges() {
    const response = await fetch('get_ranges.php');
    parameterRanges = await response.json();
}
async function fetchData() {
    const response = await fetch('https://api.thingspeak.com/channels/2732596/feeds.json?results=60');
    const data = await response.json();
    return data;
}

function getGradientColor(value, parameter) {
    if (!parameterRanges[parameter]) return 'rgb(128, 128, 128)';  // Default gray if parameter not found
    
    const ranges = parameterRanges[parameter];
    
    if (value >= ranges.ideal_min && value <= ranges.ideal_max) {
        return 'rgb(40, 167, 69)';  // Green for ideal range
    } else if ((value > ranges.ideal_max && value <= ranges.warning_max) ||
               (value >= ranges.warning_min && value < ranges.ideal_min)) {
        return 'rgb(255, 165, 0)';  // Orange for warning range
    } else {
        return 'rgb(220, 53, 69)';  // Red for out of acceptable range
    }
}

function displayLatestData(latest) {
    const container = document.getElementById('latestData');
    container.innerHTML = `
        <div class="parameter" style="background: ${getGradientColor(latest.field5, 'temperature')}">Temperature: ${latest.field5} °C</div>
        <div class="parameter" style="background: ${getGradientColor(latest.field1, 'ph')}">pH: ${latest.field1}</div>
        <div class="parameter" style="background: ${getGradientColor(latest.field2, 'turbidity')}">Turbidity: ${latest.field2} NTU</div>
        <div class="parameter" style="background: ${getGradientColor(latest.field3, 'do')}">DO: ${latest.field3} mg/L</div>
        <div class="parameter" style="background: ${getGradientColor(latest.field4, 'ec')}">EC: ${latest.field4} µS/cm</div>
        <div class="parameter" style="background: ${getGradientColor(latest.field6, 'orp')}">ORP: ${latest.field6} mV</div>
        <div class="parameter" style="background: ${getGradientColor(latest.field7, 'tds')}">TDS: ${latest.field7} PPM</div>
      `;
}

function plotChart(fieldData, title, chartDiv, yAxisLabel) {
    const timestamps = fieldData.map(feed => feed.created_at);
    const values = fieldData.map(feed => parseFloat(feed.value));

    const layout = {
        title: title,
        xaxis: {
            title: 'Timestamp',
            tickangle: 45,
            fixedrange: window.innerWidth <= 800 // Disable zooming on mobile
        },
        yaxis: {
            title: yAxisLabel,
            fixedrange: window.innerWidth <= 800 // Disable zooming on mobile
        },
        margin: { t: 40, l: 50, r: 20, b: 80 },
        plot_bgcolor: "#f0f8ff",
        paper_bgcolor: "#ffffff",
    };

    const trace = {
        x: timestamps,
        y: values,
        type: 'scatter',
        mode: 'lines+markers',
        marker: { color: 'rgb(55, 128, 191)', size: 8 },
        line: { shape: 'spline', smoothing: 1.3 }
    };

    Plotly.newPlot(chartDiv, [trace], layout, {
        responsive: true,
        staticPlot: window.innerWidth <= 800 // Make completely static on mobile
    });
}

function updateCharts(data) {
    const feeds = data.feeds.slice(-20); // Latest 20 entries

    plotChart(feeds.map(f => ({ created_at: f.created_at, value: f.field5 })),
        "Temperature (°C)", "temperatureChart", "Temperature (°C)");
    plotChart(feeds.map(f => ({ created_at: f.created_at, value: f.field1 })),
        "pH Level", "phChart", "pH Level");
    plotChart(feeds.map(f => ({ created_at: f.created_at, value: f.field2 })),
        "Turbidity (NTU)", "turbidityChart", "Turbidity (NTU)");
    plotChart(feeds.map(f => ({ created_at: f.created_at, value: f.field3 })),
        "Dissolved Oxygen (mg/L)", "DOChart", "Dissolved Oxygen (mg/L)");
    plotChart(feeds.map(f => ({ created_at: f.created_at, value: f.field4 })),
        "Electrical Conductivity (µS/cm)", "ECChart", "Electrical Conductivity (µS/cm)");
    plotChart(feeds.map(f => ({ created_at: f.created_at, value: f.field6 })),
        "ORP (mV)", "ORPChart", "ORP (mV)");
    plotChart(feeds.map(f => ({ created_at: f.created_at, value: f.field7 })),
        "TDS (PPM)", "TDSChart", "TDS (PPM)");
}

async function displayAllData(latest) {
    const temperature = parseFloat(latest.field5);
    const pH = parseFloat(latest.field1);
    const turbidity = parseFloat(latest.field2);
    const tds = parseFloat(latest.field7);
    const doLevel = parseFloat(latest.field3);
    const ec = parseFloat(latest.field4);
    const orp = parseFloat(latest.field6);

    let infoText = "<h2>Comparison of Ideal Conditions for RAS Environment</h2>";

    function getStatusMessage(value, paramName, unit) {
        const ranges = parameterRanges[paramName];
        if (!ranges) return '';

        const displayName = {
            'temperature': 'Temperature',
            'ph': 'pH level',
            'turbidity': 'Turbidity',
            'tds': 'TDS',
            'do': 'Dissolved Oxygen',
            'ec': 'Electrical Conductivity',
            'orp': 'ORP'
        }[paramName];

        if (value >= ranges.ideal_min && value <= ranges.ideal_max) {
            return `<p>✅ ${displayName} (${value} ${unit}) is ideal for the RAS environment.</p>`;
        } else if ((value > ranges.ideal_max && value <= ranges.warning_max) || 
                   (value >= ranges.warning_min && value < ranges.ideal_min)) {
            return `<p>⚠️ ${displayName} (${value} ${unit}) needs attention - outside optimal range but still acceptable.</p>`;
        } else {
            return `<p>❌ ${displayName} (${value} ${unit}) is critically outside safe range for RAS environment.</p>`;
        }
    }

    // Add status messages for each parameter
    infoText += getStatusMessage(temperature, 'temperature', '°C');
    infoText += getStatusMessage(pH, 'ph', '');
    infoText += getStatusMessage(turbidity, 'turbidity', 'NTU');
    infoText += getStatusMessage(tds, 'tds', 'PPM');
    infoText += getStatusMessage(doLevel, 'do', 'mg/L');
    infoText += getStatusMessage(ec, 'ec', 'µS/cm');
    infoText += getStatusMessage(orp, 'orp', 'mV');

    const infoBox = document.getElementById('dataInfo');
    infoBox.innerHTML = infoText;
}

function downloadData() {
    const timezone = 'Asia/Dhaka';
    const apiKey = '5FQDTVP1SQE5WKRI';
    const results = '9999999999';
    const channel= '2732596';
    const downloadLink = `https://api.thingspeak.com/channels/${channel}/feeds.csv?timezone=${timezone}&api_key=${apiKey}&results=${results}`;
    window.open(downloadLink, '_blank');
}

// Modify your init() function to fetch ranges first:
  async function init() {
    await fetchRanges();  // Fetch ranges before other data
    const data = await fetchData();
    const latest = data.feeds[data.feeds.length - 1];
    displayLatestData(latest);
    updateCharts(data);
    displayAllData(latest);
}
// Refresh data every 60 seconds
init();
setInterval(init, 15000);

function updateTime() {
    const now = new Date();
    const formattedTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('current-time').textContent = `Current Time: ${formattedTime}`;
}

// Fetch and display environmental data
async function fetchEnvironmentalData() {
    try {
        const apiKey = '23455f714f161e2988ee817bb10cf705'; // Replace with your API key
        const cityId = '1337178'; // Replace with your city ID
        const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?id=${cityId}&units=metric&appid=${apiKey}`);
        const data = await response.json();

        // Update temperature and humidity
        document.getElementById('current-temp').textContent = `Temperature: ${data.main.temp} °C`;
        document.getElementById('current-humidity').textContent = `Humidity: ${data.main.humidity} %`;
    } catch (error) {
        console.error('Error fetching environmental data:', error);
    }
}
function contactDev(){
    alert("Please Contact Developer: +8801886673292");
}

// Initialize updates
updateTime();
fetchEnvironmentalData();

// Refresh time every second
setInterval(updateTime, 1000);

// Refresh environmental data every 5 minutes
setInterval(fetchEnvironmentalData, 300000);

