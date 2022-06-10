<div class="relative box-content w-80 rounded-[54px] bg-black p-3 my-16 mx-auto hidden md:block">
  <div title="demo" loading="lazy" class="h-[680px] w-80 bg-white overflow-hidden rounded-[38px]">
    {{ $slot }}
  </div>
  <div class="pointer-events-none absolute left-1/2 top-[11px] z-50 h-[23px] w-[219px] -translate-x-1/2 bg-center bg-no-repeat" style="background-image:url(/images/notch.svg)"></div>
  <div class="pointer-events-none absolute bottom-[27px] left-1/2 z-50 h-1 w-[120px] -translate-x-1/2 rounded-full bg-[#999]"></div>
</div>