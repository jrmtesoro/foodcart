@component('mail::message')
# Order Placed

Your order **#{{ $order['code'] }}** has been placed on **{{ $order['date'] }}**.

@component('mail::panel')
##Your order will be delivered to:
**{{ ucfirst($customer['fname']).' '.ucfirst($customer['lname']) }}**
<br>
<small>**{{ $customer['address'] }}**</small>
@endcomponent
___
@foreach ($restaurant_list as $restaurant)
###{{ $restaurant['restaurant']['name'] }}
###{{ $restaurant['restaurant']['contact_number'] }}
@component('mail::table')
| Item Name | Qty | Total | Amount |
| :-: | :-: | :-: | :-: |
@foreach ($restaurant['item_list'] as $item)
| {{ $item['name'] }} | x{{ $item['quantity'] }} | ₱ {{ $item['price'] }}.00 | ₱ {{ $item['price']*$item['quantity'] }}.00 |
@endforeach
|  |  |  |  |
|  |  | <div style="text-align:right">**Delivery Charge**</div> | ₱ {{ $restaurant['restaurant']['flat_rate'] }}.00 |
|  |  | <div style="text-align:right">**Subtotal**</div> | ₱ {{ $restaurant['total']+$restaurant['restaurant']['flat_rate'] }}.00 |
|  |  | <div style="text-align:right">**Payment**</div> | ₱ {{ $restaurant['payment'] }}.00 |
|  |  | <div style="text-align:right">**Change**</div> | ₱ {{ $restaurant['payment']-($restaurant['total']+$restaurant['restaurant']['flat_rate']) }}.00 |
@endcomponent
@endforeach
___
@component('mail::panel')
<div style="text-align:right"><b>Grand Total : ₱ {{ $grand_total }}.00</b></div>
@endcomponent
@endcomponent