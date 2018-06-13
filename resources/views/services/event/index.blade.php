@extends('admin.layout')
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Event</li>
@endsection
@section('css')
@endsection

@section('content')
    <div class="card"
         style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); padding: 10px;">
        <h2 id="serviceTitle"><i class="fas fa-music" style="padding: 5px;"></i>Events<a
                    href="{{ route('event.create') }}"><i id="addGuest" class="fas fa-user-plus"></i></a></h2>
        <table class="table table-sm table-hover text-center" id="serviceTable">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <thead id="serviceTableHeader">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Guest Name</th>
                    <th scope="col">Room Nº</th>
                    <th scope="col">Event Type</th>
                    <th scope="col">Order Date</th>
                    <th scope="col">Completed?</th>
                    <th scope="col" colspan="3">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($events->sortByDesc('updated_at') as $indexKey => $event)
                    <tr>
                        <td>{{ ++$indexKey }}</td>
                        <td>{{ $event->guest->firstname . ' ' . $event->guest->lastname }}</td>
                        <td> {{ $event->guest->rooms[0]->number }} </td>
                        <td> {{ $event->eventType->name}} </td>
                        <td>{{ $event->order_date }}</td>
                        <td class="text-center"><input type="checkbox" name="{{ $event->id }}"
                                                       @if ($event->status == '2') checked @endif></td>
                        <td>
                            <a href="{{ route('event.show', $event->id) }}" class="show-modal btn btn-success">
                                <span class="far fa-eye"></span>
                            </a>
                            <a href="{{ route('event.edit', $event->id) }}" class="edit-modal btn btn-info">
                                <span class="far fa-edit"></span>
                            </a>
                            {!! Form::open(['method' => 'DELETE','route' => ['event.destroy', $event->id], 'style'=>'display:inline']) !!}
                            {!! Form::button('<span class="far fa-trash-alt"></span>', array('type' => 'submit', 'class' => 'btn-delete btn btn-danger')) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
        </table>
        <p>
        {{-- $events->render() }}
        <p>
            <span id="eventTotal">{{ $events->total() }}</span> orders | page {{ $events->currentPage() }} of {{ $events->lastPage() }}
        </p>--}}
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $(':checkbox').change(function (event) {
                var $this = this;
                var route = 'statusEvent/' + event.target.name + '';
                if ($($this).is(':checked')) {

                    toastr.options = {'closeButton': false, 'timeOut': false, 'closeOnHover': false};
                    toastr.warning('<div><button type="button" id="cancelBtn" class="btn btn-primary">Cancel</button><button type="button" id="okBtn" class="btn" style="margin: 0 8px 0 8px">Ok</button></div>', 'Is it already completed?');

                    $('#okBtn').click(function () {
                        $.get(route, function (response, state) {
                            $this.checked = true;
                            console.log("Completed " + response);
                        }).fail(function () {
                            toastr.options = {'closeButton': true, 'timeOut': 5000, 'closeOnHover': true, 'progressBar': true};
                            toastr.warning('Something went wront', 'Alert!');
                        });
                    });

                    $('#cancelBtn, #toast-container').click(function () {
                        $this.checked = false;
                    });
                } else {
                    $.get(route, function (response, state) {
                        console.log("In process " + response);
                    }).fail(function () {
                        $this.checked = true;
                        toastr.options = {'closeButton': true, 'timeOut': 5000, 'closeOnHover': true, 'progressBar': true};
                        toastr.warning('Something went wront', 'Alert!');
                    });
                }
            });

            $('.btn-delete').click(function (e) {
                var $this = this;
                e.preventDefault();
                toastr.options = {'closeButton': true, 'timeOut': false, 'closeOnHover': false};
                toastr.error('<button type="button" class="btn-yes btn">Yes</button>', 'You are about to delete a order!');
                $('.btn-yes').click(function () {
                    var row = $($this).parents('tr');
                    var form = $($this).parents('form');
                    var url = form.attr('action');

                    row.fadeOut();
                    $.post(url, form.serialize(), function (result) {
                        $('#eventTotal').html(result.total);
                        toastr.options = {'closeButton': true, 'timeOut': 5000, 'closeOnHover': true, 'progressBar': true};
                        toastr.success(result.message);
                    }).fail(function () {
                        row.fadeIn();
                        toastr.options = {'closeButton': true, 'timeOut': 5000, 'closeOnHover': true, 'progressBar': true};
                        toastr.warning('Something went wront', 'Alert!');
                    });
                });
            });
        });

    </script>
    <script>
        document.getElementsByClassName("itemDropdown")[7].style.color="white";
    </script>
@endsection