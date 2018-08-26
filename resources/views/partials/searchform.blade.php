<div class="col-12 col-lg-8 order-lg-first align-self-end">
  <form  role="search" method="get" class="form my-2 my-lg-0 search-form" action="{{ $sf_action }}">
    <div class="input-group input-group-sm">
      <input type="text" class="form-control border-radius-0" placeholder="Find your flower" value="{{ $sf_current_query }}" name="s" id="s">
      <div class="input-group-append">
        <select class="form-control form-control-sm border-radius-0">
          @php
            $categories = get_categories('taxonomy=product_cat&type=product');
          @endphp
          <option selected> in all categories</option>
          @foreach ($categories as $category)
            <option value="{{ $category->cat_ID }}">{{ $category->name }}</option>
          @endforeach
        </select>
        <input type="hidden" name="post_type" value="product">
        <button class="btn btn-outline-secondary btn-sm border-radius-0" type="submit" id="button-addon1">Button</button>
      </div>
    </div>
  </form>
</div>
