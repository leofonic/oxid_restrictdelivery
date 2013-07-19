Module for Oxid eShop

Oxid Bug #4123: 
customers can select deliveryset even if some articles in basket cannot be sent with this deliveryset

Example:
You want to let the customer choose between the cheaper book devlivery service, and the faster "normal service". So you would create a delivery set for books, and you would limit the rule to the category of books for the books delivery set. Still, as soon as you add another item to the basket, you will be able to choose the "books delivery set", even if the basket might now contain heavy goods or whatever.

Effect of this module:
If one article in has no matching rules for the delivery set, the delivery set cannot be selected by customer