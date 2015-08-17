<h1>Cards</h1>

<ul>
  {% for card in customer.creditCards %}
    <li>
      **** **** ** {{ card.bin }} 
      {% if card.default %}
        Default
      {% else %}
        <a href="/index.php/card/make_default/{{ loop.index-1 }}">Make default</a>
      {% endif %}
    </li>
  {% endfor %}
</ul>

<a href="/index.php/card/new">Add Card</a>
