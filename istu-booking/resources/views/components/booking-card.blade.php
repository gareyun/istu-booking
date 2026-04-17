@props(['booking'])

@php
    $status = '';

    if ($booking->status === 'approved') {
        $status = '✅ Одобрена';
    } elseif ($booking->status === 'rejected') {
        $status = '❌ Отклонена';
    }
@endphp

<div class="application-card" data-id="{{$booking->id}}">
    <div class="application-card-top">
        <div class="application-header">
            <div class="application-id">ID: {{$booking->id}}</div>
        </div>
        
        <div class="application-title">
            Аудитория: {{$booking->classroom->room}}
        </div>
        
        <div class="application-detail">
            <div class="label">ФИО:</div>
            <div class="value">{{$booking->user->name}}</div>
        </div>
        
        <div class="application-detail">
            <div class="label">Группа:</div>
            <div class="value">{{$booking->user->group}}</div>
        </div>
        
        <div class="application-detail">
            <div class="label">Дата:</div>
            <div class="value">{{$booking->date}}</div>
        </div>
        
        <div class="application-detail">
            <div class="label">Время:</div>
            <div class="value">{{$booking->start_time}} - {{$booking->end_time}}</div>
        </div>
        
        <div class="application-detail">
            <div class="label">Цель:</div>
            <div class="value">{{$booking->purpose}}</div>
        </div>

        <div class="application-detail">
            <div class="label">Оборудование:</div>
            <div class="value">
                @if($booking->equipment)
                    {{$booking->equipment}}
                @else
                    -
                @endif
            </div>
        </div>
        
        <div class="application-detail">
            <div class="label">Технический специалист:</div>
            <div class="value">
                @if($booking->is_tech_support)
                    Да
                @else
                    Нет
                @endif
            </div>
        </div>

        <div class="application-detail">
            <div class="label">Комментарий студента:</div>
            <div class="value application-comment">{{$booking->user_comment}}</div>
        </div>

        <div class="application-detail">
            <div class="label">Комментарий администратора:</div>
            <div class="value application-comment">
                @if($booking->admin_comment)
                    {{$booking->admin_comment}}
                @else
                    -
                @endif
            </div>
        </div>
    </div>

    @if ($booking->status == 'pending')
        <div class="admin-area">
            <form action="{{ route('booking.updateStatus', $booking) }}" method="POST">
                @csrf
                <div class="application-detail admin-message">
                    <textarea name="admin_comment"
                        class="auto-resize-textarea admin-message__input"
                        placeholder="Оставить комментарий..."
                    ></textarea>
                </div>

                <button name="action" value="approved" class="action-btn approve-btn">Принять</button>
                <button name="action" value="rejected" class="action-btn reject-btn">Отклонить</button>
            </form>
        </div>
    @else
        <div class="status-badge">{{$status}}</div>
    @endif

</div>