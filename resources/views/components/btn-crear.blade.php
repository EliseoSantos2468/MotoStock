<a 
href="#" 
{{$attributes->merge(['class' => 'rounded-lg p-2 text-white/80 hover:text-white flex items-center'])}} 
style="background-color:{{$primaryColor}};"
>
<svg
  xmlns="http://www.w3.org/2000/svg"
  viewBox="0 0 24 24"
  fill="none"
  stroke="currentColor" 
  stroke-width="1"
  stroke-linecap="round"
  stroke-linejoin="round"
  class="w-5 h-5"
>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg>
Nuevo {{$slot}}</a>
