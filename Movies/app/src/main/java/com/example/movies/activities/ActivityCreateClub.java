package com.example.movies.activities;

import android.graphics.Color;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.example.movies.R;
import com.example.movies.models.ClubResponse;
import com.example.movies.network.ClubApiService;
import com.example.movies.network.RetrofitClient;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ActivityCreateClub extends AppCompatActivity {

    ClubApiService apiService;

    EditText editInstructorId, editPassword, editClubName, editPresidentId;
    TextView tvMessage;
    Button btnCreateClub;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_create_club);
        apiService = RetrofitClient.getInstance().create(ClubApiService.class);
        initializeViews();
        setButtonListeners();
    }

    private void initializeViews() {
        editInstructorId = findViewById(R.id.editInstructorId);
        editPassword = findViewById(R.id.editPassword);
        editClubName = findViewById(R.id.editClubName);
        editPresidentId = findViewById(R.id.editPresidentId);
        tvMessage = findViewById(R.id.tvMessage);
        btnCreateClub = findViewById(R.id.btnCreateClub);
    }

    private void setButtonListeners() {
        btnCreateClub.setOnClickListener(v -> createClub());
    }

    private void createClub() {
        String instructorId = editInstructorId.getText().toString().trim();
        String password = editPassword.getText().toString().trim();
        String clubName = editClubName.getText().toString().trim();
        String presidentId = editPresidentId.getText().toString().trim();

        if (!isFormValid(instructorId, password, clubName, presidentId)) return;

        Call<ClubResponse> call = apiService.createClub(instructorId, password, clubName, presidentId);
        call.enqueue(new Callback<ClubResponse>() {
            @Override
            public void onResponse(Call<ClubResponse> call, Response<ClubResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    ClubResponse result = response.body();
                    int color = result.isSuccess() ? Color.GREEN : Color.RED;
                    showMessage(result.getMessage(), color);
                } else {
                    showMessage("Unexpected server response.", Color.RED);
                }
            }

            @Override
            public void onFailure(Call<ClubResponse> call, Throwable t) {
                showMessage("Failed to connect: " + t.getMessage(), Color.RED);
            }
        });
    }

    private boolean isFormValid(String instructorId, String password, String clubName, String presidentId) {
        if (instructorId.isEmpty()) {
            showMessage("Please enter instructor ID.", Color.RED);
            return false;
        }
        if (password.isEmpty()) {
            showMessage("Please enter password.", Color.RED);
            return false;
        }
        if (clubName.isEmpty()) {
            showMessage("Please enter club name.", Color.RED);
            return false;
        }
        if (presidentId.isEmpty()) {
            showMessage("Please enter president student ID.", Color.RED);
            return false;
        }
        return true;
    }

    private void showMessage(String message, int color) {
        tvMessage.setTextColor(color);
        tvMessage.setText(message);
    }
}
