@extends('layouts.app')

@section('content')
    <h2>สร้างกลุ่มหารเงิน (Create Group)</h2>

    <hr>

    <!-- พื้นที่แสดง Flash Message (Success / Error) -->
    <div>
        @if(session('success'))
            <div style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 10px;">
                <strong>{{ session('success') }}</strong>
            </div>
        @endif

        @if(session('error'))
            <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 10px;">
                <strong>{{ session('error') }}</strong>
            </div>
        @endif
    </div>

    <!-- พื้นที่แสดง Error จากการทำ Validation -->
    <div>
        @if($errors->any())
            <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 10px;">
                <b>พบข้อผิดพลาด:</b>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- แบบฟอร์มสร้างกลุ่ม -->
    <div style="margin-top: 20px;">
        <form action="{{ route('groups.store') }}" method="POST">
            @csrf
            
            <div>
                <label for="name">ชื่อกลุ่ม / ชื่อทริป: </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>
            
            <br>
            <button type="submit">สร้างกลุ่ม</button>
        </form>
    </div>

    <br>
    <div>
        <a href="{{ route('groups.index') }}"><< กลับหน้ารวมกลุ่ม</a>
    </div>

@endsection