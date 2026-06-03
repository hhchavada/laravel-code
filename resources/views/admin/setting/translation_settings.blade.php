@if($row->key == 'chat_gpt_api_key')
<div class="col-md-6 mb-3">
    <label class="form-label" for="chat_gpt_api_key">{{__('lang.chat_gpt_api_key')}}</label>
    <input type="text" class="form-control" placeholder="{{__('lang.chat_gpt_api_key_placeholder')}}" value="{{$row->value}}" name="chat_gpt_api_key"/>
</div>
@endif
@if($row->key == 'google_translation_api_key')
<div class="col-md-6 mb-3">
    <label class="form-label" for="google_translation_api_key">{{__('lang.google_translation_api_key')}}</label>
    <input type="text" class="form-control" placeholder="{{__('lang.google_translation_api_key_placeholder')}}" value="{{$row->value}}" name="google_translation_api_key"/>
</div>
@endif