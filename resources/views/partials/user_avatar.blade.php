@php $avatarUser = $avatarUser ?? Auth::user(); @endphp
@if($avatarUser && $avatarUser->profile_photo_url)
<div class="{{ $avatarClass ?? 'user-avatar' }}" style="padding:0;overflow:hidden;">
    <img src="{{ $avatarUser->profile_photo_url }}" alt="{{ $avatarUser->f_name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
</div>
@else
<div class="{{ $avatarClass ?? 'user-avatar' }}">
    {{ strtoupper(substr($avatarUser->f_name ?? '', 0, 1)) }}{{ strtoupper(substr($avatarUser->l_name ?? '', 0, 1)) }}
</div>
@endif
