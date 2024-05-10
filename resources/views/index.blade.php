@include('header')
<style>
.highcharts-axis-labels span{
    word-break:break-all!important;
    width:250px!important;
    white-space:normal!important;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
</style>
<div class="flex-col p-6">
    <div class="flex items-baseline justify-start space-x-2 w-full h-16 border-slate-400 bg-slate-400 border-8 rounded-lg">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
            Threshold
        </label>
        <input type="text" placeholder="Threshold" id="threshold" class="appearance-none block w-32 bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" "/>
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
            Alert type
        </label>
        <div class="relative">
        <select id="alert_type" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
            <option value="above">Above</option>
            <option value="below">Below</option>
        </select>
        </div>
        <button id="set-alert" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">Set alert</button>
        <button id="remove-alert" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">Remove alert</button>
    </div>
    <div class="flex w-full bg-slate-200 border-8 rounded-lg mt-2">
        <div id="current" style="display: none;" class="w-1/3 flex-col text-orange-900">
            <div class="font-black m-10">
                <img id="currentIcon" src="" style="width:100px;height:100px;" />
                <div>
                    <span class="text-6xl"><span id="currentTemp"></span>&deg;</span>
                    <span class="text-3xl" id="currentCondition"></span>
                </div>
                <div class="mt-3">
                    <span id="locationName" class="text-2xl font-semibold"></span>
                    <span id="currentTime" class="text-xl font-medium"></span>
                </div>
            </div>
            <div class="w-96 h-60" id="aqiGauge"></div>
        </div>
        <div class="w-2/3">
            <div style="height: 500px;" id="hourlyForecastBars"></div>
        </div>
    </div>
    <div class="w-full bg-slate-200 border-8 rounded-lg mt-10">
        <div class="m-10 w-1/4 min-w-80" onclick="event.stopImmediatePropagation();">
            <input
                id="autocompleteInput"
                placeholder="Search for location"
                class="px-5 py-3 w-full border border-gray-300 rounded-md"
                autocomplete="off"
            />
            <div
                id="autocompleteDropdown"
                class="w-1/4 min-w-80 h-60 border border-gray-300 rounded-md bg-white absolute overflow-y-auto hidden"
                style="z-index: 2000;"
            ></div>
        </div>
        <div id="map" class="w-full h-96"></div>
    </div>
</div>
@include('footer')

