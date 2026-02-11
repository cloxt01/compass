<div>
    <h2>Applied Jobs ({{ count($jobs) }})</h2>

    @if(count($jobs))
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @for($i=0; $i < 5 && $i < count($jobs); $i++)
                    <tr>
                        <td>{{ $jobs[$i]['job_title'] }}</td>
                        <td>{{ $jobs[$i]['company'] }}</td>
                        <td>{{ $jobs[$i]['job_location'] }}</td>
                        <td>{{ $jobs[$i]['status'] }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    @else
        <p>You haven't applied to any jobs yet.</p>
    @endif
</div>
