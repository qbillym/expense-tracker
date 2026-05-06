@extends('layouts.app')

@section('content')
    <h1>Edit Expense</h1>

    <form method="POST" action="{{ route('expenses.update', $expense) }}">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="title">Title</label>
            <input id="title" type="text" name="title" value="{{ old('title', $expense->title) }}" required>
        </div>

        <div class="field">
            <label for="amount">Amount</label>
            <input id="amount" type="number" name="amount" value="{{ old('amount', $expense->amount) }}" step="0.01" required>
        </div>

        <div class="field">
            <label for="category">Category</label>
            <input id="category" type="text" name="category" value="{{ old('category', $expense->category) }}" required>
        </div>

        <div class="field">
            <label for="date">Date</label>
            <input id="date" type="date" name="date" value="{{ old('date', $expense->date->format('Y-m-d')) }}" required>
        </div>

        <div class="field">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes">{{ old('notes', $expense->notes) }}</textarea>
        </div>

        <button class="button" type="submit">Update Expense</button>
    </form>
@endsection