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
    <div style="display: flex;" class="w-full bg-slate-200 border-8 rounded-lg">
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

