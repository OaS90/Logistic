<p>Имя: {{ $partner->firstname }}</p>
<p>Отчество: {{ $partner->patronymic }}</p>
<p>Фамилия: {{ $partner->lastname }}</p>
<p>Мобильный телефон: {{ $partner->phone }}</p>
<p>Должность: {{ $partner->position }}</p>
<p>Рабочий телефон: {{ $partner->work_phone}}</p>
<p>Название компании: {{ $partner->company }}</p>
<p>ИНН {{ $partner->inn }}</p>
<p>КПП {{ $partner->kpp }}</p>
<p>ОКПО {{ $partner->okpo }}</p>
<p>Юридический адрес: {{ $partner->legal_address }}</p>
<p>Email: {{ $partner->email }}</p>
<hr>
<ul>
@foreach($partner->warehouses as $warehouse)
    <li>Адрес склада: {{ $warehouse->address }}</li>
@endforeach
</ul>









