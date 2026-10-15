@if(session('success'))
    <div class="cc-notice cc-notice-success" role="status">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="cc-notice cc-notice-error" role="alert">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="cc-notice cc-notice-error" role="alert">
        <p class="font-semibold">Revise la información ingresada.</p>
        <ul class="mt-2 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
